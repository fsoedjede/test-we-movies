<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

final class RouteControllerTest extends WebTestCase
{
    public function testRouteDoesNotProduceError(): void
    {
        $client = static::createClient();
        $routes = self::getContainer()
            ->get(RouterInterface::class)
            ->getRouteCollection();

        /** @var Route $route */
        foreach ($routes as $route) {
            $methods = $route->getMethods();
            if (empty($methods)) {
                $methods[] = 'GET';
            }

            foreach ($methods as $method) {
                $client->request($method, $route->getPath());

                self::assertLessThan(
                    500,
                    $client->getResponse()->getStatusCode(),
                    sprintf('Route "%s" returns 500', $route->getPath())
                );
            }
        }
    }
}
