<?php

namespace AftDevTest\ServiceManager\Resolver;

use AftDev\ServiceManager\Middleware\ResolveMiddleware;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @internal
 * @covers \AftDev\ServiceManager\Middleware\ResolveMiddleware
 */
final class ResolveMiddlewareTest extends TestCase
{
    public function testMiddleware()
    {
        $request = new ServerRequest();

        $attributes = [
            'test' => 'testValue',
        ];

        foreach ($attributes as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        $response = $this->prophesize(ResponseInterface::class);
        $requestHandler = $this->prophesize(RequestHandlerInterface::class);

        $handlerName = 'XXXX';
        $resolver = $this->prophesize(Resolver::class);
        $resolver
            ->call($handlerName, $attributes)
            ->shouldBeCalledOnce()
            ->willReturn($response->reveal())
        ;

        $resolveMiddleware = new ResolveMiddleware($resolver->reveal(), $handlerName);
        $resolveMiddleware->process($request, $requestHandler->reveal());
    }
}
