<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PopularMovieRepository
{
    private const EXPIRATION_TIME = 15 * 60; // 15 min

    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function list(int $page = 1): array
    {
        try {
            return $this->cache->get('themoviedb.movie_popular.'.$page, function (ItemInterface $item) use ($page): array {
                $item->expiresAfter(self::EXPIRATION_TIME);

                $data = $this->fetchFromApi($page);

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

    public function first(): ?array
    {
        $list = $this->list();

        return array_shift($list);
    }

    private function fetchFromApi(int $page = 1): array
    {
        try {
            $response = $this->theMovieDbClient->request('GET', 'movie/popular', [
                'query' => [
                    'page' => $page,
                ]
            ]);

            $responseData = $response->toArray();
            if ($response->getStatusCode() === 200) {
                return $responseData['results'];
            }

            throw new \DomainException(
                sprintf(
                    'API ERROR: Popular movie list: (Code %d) %s',
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
