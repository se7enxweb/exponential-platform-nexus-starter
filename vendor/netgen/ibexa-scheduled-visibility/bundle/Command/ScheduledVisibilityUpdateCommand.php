<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Command;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\Configuration;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\Exception\InvalidStateException;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\ScheduledVisibilityService;
use Pagerfanta\Doctrine\DBAL\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function count;
use function sprintf;
use function time;

final class ScheduledVisibilityUpdateCommand extends Command
{
    private SymfonyStyle $style;
    private array $languageCache = [];

    public function __construct(
        private readonly Repository $repository,
        private readonly ContentService $contentService,
        private readonly ContentTypeService $contentTypeService,
        private readonly LanguageService $languageService,
        private readonly ScheduledVisibilityService $scheduledVisibilityService,
        private readonly Configuration $configurationService,
        private readonly Connection $connection,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Updates content visibility based on publish_from and publish_to attributes and configuration.',
        );

        $this->addOption(
            'limit',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Number of content objects to process in a single iteration',
            1024,
        );

        $this->addOption(
            'since',
            null,
            InputOption::VALUE_OPTIONAL,
            'Process Content Items modified since the given number of days',
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->style = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->style->info(
            'This command fetches content and updates visibility based on its schedule from publish_from and publish_to fields.',
        );

        $question = new ConfirmationQuestion(
            'Continue with this action?',
            true,
            '/^(y)/i',
        );

        if (!$this->style->askQuestion($question)) {
            $this->style->success('Aborted');

            return Command::SUCCESS;
        }

        if (!$this->configurationService->isEnabled()) {
            $this->style->warning('Scheduled visibility mechanism is disabled.');

            return Command::FAILURE;
        }

        $allContentTypes = $this->configurationService->isAllContentTypes();
        $allowedContentTypes = $this->configurationService->getAllowedContentTypes();

        if (!$allContentTypes && count($allowedContentTypes) === 0) {
            $this->style->warning('No content types configured for scheduled visibility mechanism.');

            return Command::FAILURE;
        }

        $since = $input->getOption('since');
        $since = $since === null ? $since : (int) $since;
        $pager = $this->getPager($since);

        if ($pager->getNbResults() === 0) {
            $this->style->info('No content found');

            return Command::FAILURE;
        }

        $limit = $input->getOption('limit');
        $offset = 0;

        $progressBar = $this->style->createProgressBar($pager->getNbResults());
        $progressBar->setFormat('debug');
        $progressBar->start();

        $results = $pager->getAdapter()->getSlice($offset, $limit);
        while (count($results) > 0) {
            $this->processResults($results, $progressBar);
            $offset += $limit;
            $results = $pager->getAdapter()->getSlice($offset, $limit);
        }

        $progressBar->finish();

        $this->style->info('Done.');

        return Command::SUCCESS;
    }

    private function processResults(array $results, ProgressBar $progressBar): void
    {
        foreach ($results as $result) {
            try {
                $languageId = (int) $result['initial_language_id'];
                $language = $this->loadLanguage($languageId);
            } catch (NotFoundException $exception) {
                $this->logger->error(
                    sprintf(
                        'Language with id #%d does not exist: %s',
                        $languageId,
                        $exception->getMessage(),
                    ),
                );

                $progressBar->advance();

                continue;
            }

            try {
                $contentId = $result['id'];

                /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
                $content = $this->repository->sudo(
                    fn () => $this->contentService->loadContent(
                        $contentId,
                        [$language->getLanguageCode()],
                    ),
                );
            } catch (Throwable $throwable) {
                $this->logger->error(
                    sprintf(
                        'An error occurred when loading Content #%d: %s',
                        $contentId,
                        $throwable->getMessage(),
                    ),
                );

                $progressBar->advance();

                continue;
            }

            try {
                $this->scheduledVisibilityService->updateVisibilityIfNeeded($content);
            } catch (InvalidStateException $exception) {
                $this->logger->error($exception->getMessage());
            }

            $progressBar->advance();
        }
    }

    /**
     * @param int[] $contentTypeIds
     */
    private function getQueryBuilder(?int $since, array $contentTypeIds): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('id', 'initial_language_id')
            ->from('ibexa_content')
            ->where('published != :unpublished')
            ->andWhere('status != :trashed')
            ->orderBy('id', 'ASC')
            ->setParameter('unpublished', 0)
            ->setParameter('trashed', ContentInfo::STATUS_TRASHED);

        $this->applySince($queryBuilder, $since);
        $this->applyContentTypes($queryBuilder, $contentTypeIds);

        return $queryBuilder;
    }

    private function applySince(QueryBuilder $queryBuilder, ?int $since): void
    {
        if ($since === null) {
            return;
        }

        $since = time() - ($since * 86400);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->gte('modified', ':modified'))
            ->setParameter('modified', $since)
        ;
    }

    /**
     * @param int[] $contentTypeIds
     */
    private function applyContentTypes(QueryBuilder $query, array $contentTypeIds): void
    {
        if (count($contentTypeIds) === 0) {
            return;
        }

        $query
            ->andWhere($query->expr()->in('content_type_id', ':content_type_ids'))
            ->setParameter('content_type_ids', $contentTypeIds, ArrayParameterType::INTEGER)
        ;
    }

    private function getPager(?int $since): Pagerfanta
    {
        $contentTypeIds = $this->getContentTypeIds();

        $queryBuilder = $this->getQueryBuilder($since, $contentTypeIds);

        $countQueryBuilderModifier = function (QueryBuilder $queryBuilder) use ($since, $contentTypeIds): void {
            $queryBuilder->select('COUNT(id) AS total_results')
                ->from('ibexa_content')
                ->where('published != :unpublished')
                ->setParameter('unpublished', 0)
                ->setParameter('trashed', ContentInfo::STATUS_TRASHED)
                ->setMaxResults(1);

            $this->applySince($queryBuilder, $since);
            $this->applyContentTypes($queryBuilder, $contentTypeIds);
        };

        return new Pagerfanta(new QueryAdapter($queryBuilder, $countQueryBuilderModifier));
    }

    /**
     * @return int[]
     */
    private function getContentTypeIds(): array
    {
        $allContentTypes = $this->configurationService->isAllContentTypes();
        $allowedContentTypes = $this->configurationService->getAllowedContentTypes();

        if ($allContentTypes || count($allowedContentTypes) === 0) {
            return [];
        }

        $contentTypeIds = [];

        foreach ($allowedContentTypes as $allowedContentType) {
            try {
                $contentTypeIds[] = $this->contentTypeService->loadContentTypeByIdentifier($allowedContentType)->id;
            } catch (NotFoundException $exception) {
                $this->logger->error(
                    sprintf(
                        "Content type with identifier '%s' does not exist: %s",
                        $allowedContentType,
                        $exception->getMessage(),
                    ),
                );

                continue;
            }
        }

        return $contentTypeIds;
    }

    private function loadLanguage(int $id): Language
    {
        if (!isset($this->languageCache[$id])) {
            $language = $this->languageService->loadLanguageById($id);
            $this->languageCache[$id] = $language;
        }

        return $this->languageCache[$id];
    }
}
