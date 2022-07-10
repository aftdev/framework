<?php

namespace AftDevTest\ServiceManager\Resolver;

use AftDev\ServiceManager\Middleware\ResolveMiddlewareFactory;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @internal
 * @covers \AftDev\ServiceManager\Middleware\ResolveMiddlewareFactory
 */
final class ResolveMiddlewareFactoryTest extends TestCase
{
    public function testFactory()
    {
        $resolver = $this->prophesize(Resolver::class);

        $factory = new ResolveMiddlewareFactory($resolver->reveal());

        $middleware = $factory->prepare('Test');

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }
}
