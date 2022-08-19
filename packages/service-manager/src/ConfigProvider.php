<?php

namespace AftDev\ServiceManager;

use AftDev\ServiceManager\Factory\ResolverAbstractFactory;
use AftDev\ServiceManager\Middleware\ResolveMiddlewareFactory;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies()
    {
        return [
            'factories' => [
                Resolver::class => Resolver\ResolverFactory::class,
                ResolveMiddlewareFactory::class => Middleware\ResolveMiddlewareFactoryFactory::class,
            ],
            'abstract_factories' => [
                'resolver' => ResolverAbstractFactory::class,
            ],
        ];
    }
}
