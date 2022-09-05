<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MovieGenreRepository
{
    private const EXPIRATION_TIME = 60 * 60; // 1 hour

    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function list(): array
    {
        try {
            return $this->cache->get('themoviedb.movie_genre', function (ItemInterface $item): array {
                $item->expiresAfter(self::EXPIRATION_TIME);

                $data = $this->fetchFromApi();

                if (empty($data)) {
                    // Prevent cache when empty
                    $item->expiresAfter(1);

                    return [];
                }

                return $data;
            });
        } catch (\Throwable $exception) {
            //@todo: use logger
            return [];
        }
    }

    private function fetchFromApi(): array
    {
        try {
            $response = $this->theMovieDbClient->request('GET', 'genre/movie/list');

            $responseData = $response->toArray();
            if ($response->getStatusCode() === 200) {
                return $responseData['genres'];
            }

            throw new \DomainException(
                sprintf(
                    'API ERROR: Genre list: (Code %d) %s',
                    $responseData['status_code'],
                    $responseData['status_message']
                )
            );
        } catch (\Throwable $e) {
            //@todo: use logger

            return [];
        }
    }
}
