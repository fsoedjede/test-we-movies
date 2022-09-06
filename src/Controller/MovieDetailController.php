<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ConfigurationRepository;
use App\Repository\MovieDetailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MovieDetailController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationRepository $configurationRepository,
        private readonly MovieDetailRepository $movieDetailRepository,
    ) {
    }

    public function __invoke(int $movieId): Response
    {
        return $this->render('view.html.twig', [
            'config' => $this->configurationRepository->get(),
            'movie'  => $this->movieDetailRepository->get($movieId),
        ]);
    }
}
