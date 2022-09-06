<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SearchMovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchController extends AbstractController
{
    public function __construct(
        private readonly SearchMovieRepository $searchMovieRepository,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return new JsonResponse(
            $this->searchMovieRepository->search($request->query->get('query', ''))
        );
    }
}
