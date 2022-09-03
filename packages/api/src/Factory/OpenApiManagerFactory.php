<?php

declare(strict_types=1);

namespace AftDev\Api\Factory;

use AftDev\Api\ConfigProvider;
use AftDev\Api\OpenApiManager;
use AftDev\ServiceManager\Resolver;
use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class OpenApiManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')[ConfigProvider::CONFIG_KEY];
        $resolver = $container->has(Resolver::class) ? $container->get(Resolver::class) : null;

        $version = $this->getApiVersionFromHeader($container) ?? $config['version'] ?? null;

        return new OpenApiManager(
            specFile: $config['spec'],
            currentVersion: $version,
            versions: $config['versions'] ?? [],
            resolver: $resolver,
        );
    }

    private function getApiVersionFromHeader(ContainerInterface $container): ?string
    {
        $request = $container->has(ServerRequestInterface::class) ? $container->get(ServerRequestInterface::class) : null;

        // Mezzio for some reason use a callback for the service.
        if ($request instanceof Closure) {
            $request = $request();
        }

        if (!$request) {
            return null;
        }

        return $request->getHeader(OpenApiManager::VERSION_HEADER_NAME, null)[0] ?? null;
    }
}
