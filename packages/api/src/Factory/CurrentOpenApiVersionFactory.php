<?php

declare(strict_types=1);

namespace AftDev\Api\Factory;

use AftDev\Api\OpenApiManager;
use Psr\Container\ContainerInterface;

class CurrentOpenApiVersionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $apiManager = $container->get(OpenApiManager::class);

        return $apiManager->getCurrentVersion();
    }
}
