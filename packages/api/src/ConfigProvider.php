<?php

declare(strict_types=1);

namespace AftDev\Api;

use AftDev\Api\Route\FastRouterParamTranslator;
use AftDev\Api\Route\HandlerMapper;
use AftDev\Api\Route\OpenApiRouteGenerator;
use AftDev\Api\Route\ParamTranslatorInterface;
use cebe\openapi\spec\OpenApi;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public const CONFIG_KEY = 'api';

    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            self::CONFIG_KEY => $this->getApiConfig(),
        ];
    }

    public function getDependencies()
    {
        return [
            'factories' => [
                OpenApi::class => Factory\CurrentOpenApiVersionFactory::class,
                OpenApiManager::class => Factory\OpenApiManagerFactory::class,
                OpenApiRouteGenerator::class => Factory\OpenApiRouteGeneratorFactory::class,
                HandlerMapper::class => InvokableFactory::class,
                FastRouterParamTranslator::class => InvokableFactory::class,
                \League\OpenAPIValidation\PSR15\ValidationMiddleware::class => Factory\RequestValidationMiddlewareFactory::class,
            ],
            'aliases' => [
                ParamTranslatorInterface::class => FastRouterParamTranslator::class,
            ],
        ];
    }

    public function getApiConfig()
    {
        return [
            'servers' => [
                'default' => [
                    'url' => 'https://api.your-site.com',
                ],
            ],
            'spec' => 'config/openapi/openapi.yml',
            'namespace' => 'App\Api\Controller',
            'version' => null,
            'versions' => [],
            // Cache store to use (requires aftdev/cache-manager)
            'cache' => null,
        ];
    }
}
