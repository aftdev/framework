<?php

declare(strict_types=1);

namespace AftDev\Api\Factory;

use AftDev\Api\ConfigProvider;
use AftDev\Api\Route\OpenApiRouteGenerator;
use AftDev\Api\Route\ParamTranslatorInterface;
use Psr\Container\ContainerInterface;

class OpenApiRouteGeneratorFactory
{
    use InteractsWithCacheTrait;

    public function __invoke(ContainerInterface $container)
    {
        $paramTranslator = $container->get(ParamTranslatorInterface::class);

        $config = $container->get('config')[ConfigProvider::CONFIG_KEY];
        $prefix = $config['prefix'] ?? null;
        $namespace = $config['prefix'] ?? 'App/Controller';
        $cache = $this->getCache($container, $config);

        return new OpenApiRouteGenerator(
            prefix: $prefix,
            namespace: $namespace,
            paramTranslator: $paramTranslator,
            cache: $cache,
        );
    }
}