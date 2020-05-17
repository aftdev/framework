<?php

namespace AftDev\DbEloquent\Middleware;

use AftDev\DbEloquent\Capsule\CapsuleManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BootEloquent implements MiddlewareInterface
{
    /**
     * @var CapsuleManager
     */
    protected $capsuleManager;

    public function __construct(CapsuleManager $capsuleManager)
    {
        $this->capsuleManager = $capsuleManager;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->capsuleManager->bootEloquent();

        return $handler->handle($request);
    }
}
