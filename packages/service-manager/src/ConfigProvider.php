<?php

namespace AftDev\ServiceManager;

use AftDev\ServiceManager\Factory\ResolverAbstractFactory;

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
            ],
            'abstract_factories' => [
                'default' => ResolverAbstractFactory::class,
            ],
        ];
    }
}
