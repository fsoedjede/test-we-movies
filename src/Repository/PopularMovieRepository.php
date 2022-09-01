<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PopularMovieRepository
{
    public function __construct(
        private readonly HttpClientInterface $theMovieDbClient,
    ) {
    }

    public function list(int $page = 1): array
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

    public function first(): ?array
    {
        $list = $this->list();

        return array_shift($list);
    }
}
