<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MovieDetailRepository
{
    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
    ) {
    }

    public function get(int $movieId): array
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
