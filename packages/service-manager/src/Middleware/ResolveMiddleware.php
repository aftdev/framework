<?php

declare(strict_types=1);

namespace AftDev\ServiceManager\Middleware;

use AftDev\ServiceManager\Resolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResolveMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Resolver $resolver,
        private string $callable
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $attributes = $request->getAttributes();
        $attributes[ServerRequestInterface::class] = $request;

        return $this->resolver->call($this->callable, $attributes);
    }
}
