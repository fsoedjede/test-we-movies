<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ConfigurationRepository;
use App\Repository\DiscoverMovieRepository;
use App\Repository\MovieGenreRepository;
use App\Repository\PopularMovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly MovieGenreRepository $movieGenreRepository,
        private readonly PopularMovieRepository $popularMovieRepository,
        private readonly DiscoverMovieRepository $discoverMovieRepository,
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function __invoke(): Response
    {
        $firstPopularMovie = $this->popularMovieRepository->first();

        return $this->render('index.html.twig', [
            'config'            => $this->configurationRepository->get(),
            'movies'            => $this->discoverMovieRepository->list(),
            'genres'            => $this->movieGenreRepository->list(),
            'firstPopularMovie' => $firstPopularMovie,
        ]);
    }
}
