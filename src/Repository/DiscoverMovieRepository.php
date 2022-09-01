<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DiscoverMovieRepository
{
    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
    ) {
    }

    public function list(int $page = 1, array $filters = []): array
    {
        try {
            $query = [
                'page' => $page,
                'append_to_response' => 'videos,images',
            ];

            $query += $filters;

            $response = $this->theMovieDbClient->request('GET', 'discover/movie', [
                'query' => $query
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
