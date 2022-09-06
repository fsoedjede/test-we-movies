<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ConfigurationRepository
{
    private const EXPIRATION_TIME = 60 * 60; // 1 hour

    private array $defaults = [
        'images' => [
            'base_url' => 'http://image.tmdb.org/t/p/',
            'secure_base_url' => 'https://image.tmdb.org/t/p/',
        ]
    ];

    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function get(): array
    {
        try {
            return $this->cache->get('themoviedb.configuration', function (ItemInterface $item): array {
                $item->expiresAfter(self::EXPIRATION_TIME);

                $data = $this->fetchFromApi();

                if (empty($data)) {
                    // Prevent cache when empty
                    $item->expiresAfter(1);

                    return $this->defaults;
                }

                return $data;
            });
        } catch (\Throwable $exception) {
            //@todo: use logger
            return $this->defaults;
        }
    }

    private function fetchFromApi(): array
    {
        try {
            $response = $this->theMovieDbClient->request('GET', 'configuration');

            $responseData = $response->toArray();
            if ($response->getStatusCode() === 200) {
                return $responseData;
            }

            throw new \DomainException(
                sprintf(
                    'API ERROR: Configuration: (Code %d) %s',
                    $responseData['status_code'],
                    $responseData['status_message']
                )
            );
        } catch (\Throwable $e) {
            //@todo: use logger

            return $this->defaults;
        }
    }
}
