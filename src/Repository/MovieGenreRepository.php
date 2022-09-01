<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MovieGenreRepository
{
    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
    ) {
    }

    public function list(): array
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
