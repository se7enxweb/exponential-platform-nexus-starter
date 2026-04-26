<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\SourceFetcher;

use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception\PageUnavailableException;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\SourceFetcher;
use Symfony\Component\HttpClient\HttpClient;
use Throwable;

final class NativeSourceFetcher extends SourceFetcher
{
    /**
     * @inheritDoc
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \JsonException
     */
    public function fetchSource(string $url): string
    {
        $response = HttpClient::create()->request('GET', $url);

        try {
            $html = $response->getContent(false);
            $statusCode = $response->getStatusCode();
        } catch (Throwable $throwable) {
            throw new PageUnavailableException($url, $throwable->getMessage());
        }

        if ($statusCode !== 200) {
            throw new PageUnavailableException($url, json_encode($response->getInfo(), JSON_THROW_ON_ERROR));
        }

        return $html;
    }
}
