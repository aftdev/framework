<?php

namespace AftDev\Test\Feature\ServiceManager;

use AftDev\ServiceManager\Middleware\ResolveMiddlewareFactory;
use AftDev\Test\FeatureTestCase;

/**
 * @internal
 * @covers \AftDev\ServiceManager\ConfigProvider
 * @covers \AftDev\ServiceManager\Middleware\ResolveMiddleware
 * @covers \AftDev\ServiceManager\Middleware\ResolveMiddlewareFactory
 * @covers \AftDev\ServiceManager\Middleware\ResolveMiddlewareFactoryFactory
 */
class ResolveMiddlewareTest extends FeatureTestCase
{
    /**
     * Test that the service get created properly.
     */
    public function testService()
    {
        $factory = $this->container->get(ResolveMiddlewareFactory::class);

        $this->assertInstanceOf(ResolveMiddlewareFactory::class, $factory);
    }
}
