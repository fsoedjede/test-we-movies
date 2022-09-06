<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SearchMovieRepository
{
    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
    ) {
    }

    public function search(string $query, int $page = 1): array
    {
        try {
            $response = $this->theMovieDbClient->request('GET', 'search/movie', [
                'query' => [
                    'query' => urlencode($query),
                    'page' => $page,
                ]
            ]);

            $responseData = $response->toArray();
            if ($response->getStatusCode() === 200) {
                return $responseData['results'];
            }

            throw new \DomainException(
                sprintf(
                    'API ERROR: Search movie: (Code %d) %s',
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
