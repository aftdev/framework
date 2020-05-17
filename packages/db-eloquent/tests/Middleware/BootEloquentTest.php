<?php

namespace AftDevTest\DbEloquent\Middleware;

use AftDev\DbEloquent\Capsule\CapsuleManager;
use AftDev\DbEloquent\Middleware\BootEloquent;
use AftDev\Test\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class BootEloquentTest.
 *
 * @covers \AftDev\DbEloquent\Middleware\BootEloquent
 *
 * @internal
 */
class BootEloquentTest extends TestCase
{
    public function testMiddleware()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $capsuleManager = $this->prophesize(CapsuleManager::class);
        $middleware = new BootEloquent($capsuleManager->reveal());

        $capsuleManager->bootEloquent()->shouldBeCalledOnce();
        $handler->handle(Argument::any())->shouldBeCalledOnce();

        $middleware->process($request->reveal(), $handler->reveal());
    }
}
