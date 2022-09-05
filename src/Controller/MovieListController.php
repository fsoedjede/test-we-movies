<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ConfigurationRepository;
use App\Repository\DiscoverMovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MovieListController extends AbstractController
{
    public function __construct(
        private readonly DiscoverMovieRepository $discoverMovieRepository,
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $params = $request->query->all();

        return $this->render('partials/_movie_list.html.twig', [
            'config' => $this->configurationRepository->get(),
            'movies' => $this->discoverMovieRepository->list(filters: $params)
        ]);
    }
}
