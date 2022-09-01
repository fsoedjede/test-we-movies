<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\MovieDetailRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MovieDetailTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly MovieDetailRepository $movieDetailRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_movie_detail', [$this->movieDetailRepository, 'get']),
        ];
    }
}
