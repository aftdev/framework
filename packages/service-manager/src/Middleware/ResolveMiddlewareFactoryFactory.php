<?php

namespace AftDev\ServiceManager\Middleware;

use AftDev\ServiceManager\Resolver;
use Psr\Container\ContainerInterface;

class ResolveMiddlewareFactoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ResolveMiddlewareFactory($container->get(Resolver::class));
    }
}
