<?php

declare(strict_types=1);

namespace AftDev\Api\Factory;

use AftDev\Api\OpenApiManager;
use League\OpenAPIValidation\PSR15\ValidationMiddlewareBuilder;
use Psr\Container\ContainerInterface;

class RequestValidationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $apiManager = $container->get(OpenApiManager::class);

        $currentVersion = $apiManager->getCurrentVersion();

        return (new ValidationMiddlewareBuilder())
            ->fromSchema($currentVersion)
            ->getValidationMiddleware()
        ;
    }
}
