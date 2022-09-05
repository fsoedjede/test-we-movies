<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MovieDetailRepository
{
    private const EXPIRATION_TIME = 15 * 60; // 15 min

    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function get(int $movieId): array
    {
        try {
            return $this->cache->get('themoviedb.movie_genre.'.$movieId, function (ItemInterface $item) use ($movieId): array {
                $item->expiresAfter(self::EXPIRATION_TIME);

                $data = $this->fetchFromApi($movieId);

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

    private function fetchFromApi(int $movieId): array
    {
        try {
            $response = $this->theMovieDbClient->request('GET', 'movie/' . $movieId, [
                'query' => [
                    'append_to_response' => 'videos,images',
                ],
            ]);

            $responseData = $response->toArray();
            if ($response->getStatusCode() === 200) {
                return $responseData;
            }

            throw new \DomainException(
                sprintf(
                    'API ERROR: Movie detail: (Code %d) %s',
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
