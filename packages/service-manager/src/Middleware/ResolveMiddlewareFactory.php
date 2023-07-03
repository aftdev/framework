<?php

declare(strict_types=1);

namespace AftDev\ServiceManager\Middleware;

use AftDev\ServiceManager\Resolver;

class ResolveMiddlewareFactory
{
    public function __construct(
        private Resolver $resolver,
    ) {
    }

    public function prepare(string $callable): ResolveMiddleware
    {
        return new ResolveMiddleware($this->resolver, $callable);
    }
}
