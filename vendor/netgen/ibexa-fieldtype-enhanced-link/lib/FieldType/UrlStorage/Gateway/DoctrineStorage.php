<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\FieldType\UrlStorage\Gateway;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Ibexa\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase;
use Netgen\IbexaFieldTypeEnhancedLink\FieldType\UrlStorage\Gateway;
use PDO;

use function md5;
use function time;

class DoctrineStorage extends Gateway
{
    public const string URL_TABLE = DoctrineDatabase::URL_TABLE;
    public const string URL_LINK_TABLE = DoctrineDatabase::URL_LINK_TABLE;

    public function __construct(
        protected Connection $connection,
    ) {}

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getIdUrlMap(array $ids): array
    {
        $map = [];

        if (!empty($ids)) {
            $query = $this->connection->createQueryBuilder();
            $query
                ->select(
                    $this->connection->quoteIdentifier('id'),
                    $this->connection->quoteIdentifier('url'),
                )
                ->from(self::URL_TABLE)
                ->where('id IN (:ids)')
                ->setParameter('ids', $ids, ArrayParameterType::INTEGER);

            $statement = $query->executeQuery();
            foreach ($statement->fetchAllAssociative() as $row) {
                $map[$row['id']] = $row['url'];
            }
        }

        return $map;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getUrlIdMap(array $urls): array
    {
        $map = [];

        if (!empty($urls)) {
            $query = $this->connection->createQueryBuilder();
            $query
                ->select(
                    $this->connection->quoteIdentifier('id'),
                    $this->connection->quoteIdentifier('url'),
                )
                ->from(self::URL_TABLE)
                ->where(
                    $query->expr()->in('url', ':urls'),
                )
                ->setParameter('urls', $urls, ArrayParameterType::STRING);

            $statement = $query->executeQuery();

            foreach ($statement->fetchAllAssociative() as $row) {
                $map[$row['url']] = (int)$row['id'];
            }
        }

        return $map;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insertUrl(string $url): int
    {
        $time = time();

        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::URL_TABLE))
            ->values(
                [
                    'created' => ':created',
                    'modified' => ':modified',
                    'original_url_md5' => ':original_url_md5',
                    'url' => ':url',
                ],
            )
            ->setParameter('created', $time, PDO::PARAM_INT)
            ->setParameter('modified', $time, PDO::PARAM_INT)
            ->setParameter('original_url_md5', md5($url))
            ->setParameter('url', $url)
        ;

        $query->executeStatement();

        return (int) $this->connection->lastInsertId(
            $this->getSequenceName(self::URL_TABLE, 'id'),
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function linkUrl(int $urlId, int $fieldId, int $versionNo): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::URL_LINK_TABLE))
            ->values(
                [
                    'contentobject_attribute_id' => ':contentobject_attribute_id',
                    'contentobject_attribute_version' => ':contentobject_attribute_version',
                    'url_id' => ':url_id',
                ],
            )
            ->setParameter('contentobject_attribute_id', $fieldId, PDO::PARAM_INT)
            ->setParameter('contentobject_attribute_version', $versionNo, PDO::PARAM_INT)
            ->setParameter('url_id', $urlId, PDO::PARAM_INT)
        ;

        $query->executeStatement();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function unlinkUrl(int $fieldId, int $versionNo, array $excludeUrlIds = []): void
    {
        $selectQuery = $this->connection->createQueryBuilder();
        $selectQuery
            ->select('link.url_id')
            ->from($this->connection->quoteIdentifier(self::URL_LINK_TABLE), 'link')
            ->where(
                $selectQuery->expr()->and(
                    $selectQuery->expr()->in(
                        'link.contentobject_attribute_id',
                        ':contentobject_attribute_id',
                    ),
                    $selectQuery->expr()->in(
                        'link.contentobject_attribute_version',
                        ':contentobject_attribute_version',
                    ),
                ),
            )
            ->setParameter('contentobject_attribute_id', $fieldId, ParameterType::INTEGER)
            ->setParameter('contentobject_attribute_version', $versionNo, ParameterType::INTEGER);

        $statement = $selectQuery->executeQuery();
        $potentiallyOrphanedUrls = $statement->fetchFirstColumn();

        if (empty($potentiallyOrphanedUrls)) {
            return;
        }

        $deleteQuery = $this->connection->createQueryBuilder();
        $deleteQuery
            ->delete($this->connection->quoteIdentifier(self::URL_LINK_TABLE))
            ->where(
                $deleteQuery->expr()->and(
                    $deleteQuery->expr()->in(
                        'contentobject_attribute_id',
                        ':contentobject_attribute_id',
                    ),
                    $deleteQuery->expr()->in(
                        'contentobject_attribute_version',
                        ':contentobject_attribute_version',
                    ),
                ),
            )
            ->setParameter('contentobject_attribute_id', $fieldId, ParameterType::INTEGER)
            ->setParameter('contentobject_attribute_version', $versionNo, ParameterType::INTEGER);

        if (empty($excludeUrlIds) === false) {
            $deleteQuery
                ->andWhere(
                    $deleteQuery->expr()->notIn(
                        'url_id',
                        ':url_ids',
                    ),
                )
                ->setParameter('url_ids', $excludeUrlIds, ArrayParameterType::INTEGER);
        }

        $deleteQuery->executeStatement();

        $this->deleteOrphanedUrls($potentiallyOrphanedUrls);
    }

    /**
     * Delete potentially orphaned URLs.
     *
     * That could be avoided if the feature is implemented there.
     *
     * URL is orphaned if it is not linked to a content attribute through ezurl_object_link table.
     *
     * @param int[] $potentiallyOrphanedUrls
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteOrphanedUrls(array $potentiallyOrphanedUrls): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select($this->connection->quoteIdentifier('url.id'))
            ->from($this->connection->quoteIdentifier(self::URL_TABLE), 'url')
            ->leftJoin(
                'url',
                $this->connection->quoteIdentifier(self::URL_LINK_TABLE),
                'link',
                'url.id = link.url_id',
            )
            ->where(
                $query->expr()->in(
                    'url.id',
                    ':url_ids',
                ),
            )
            ->andWhere($query->expr()->isNull('link.url_id'))
            ->setParameter('url_ids', $potentiallyOrphanedUrls, ArrayParameterType::INTEGER)
        ;

        $statement = $query->executeQuery();
        $ids = $statement->fetchFirstColumn();

        if (empty($ids)) {
            return;
        }

        $deleteQuery = $this->connection->createQueryBuilder();
        $deleteQuery
            ->delete($this->connection->quoteIdentifier(self::URL_TABLE))
            ->where($deleteQuery->expr()->in('id', ':ids'))
            ->setParameter('ids', $ids, ArrayParameterType::STRING)
        ;

        $deleteQuery->executeStatement();
    }
}
