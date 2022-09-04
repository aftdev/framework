<?php

namespace AftDev\Test\Feature\ServiceManager;

use AftDev\ServiceManager\Resolver;
use AftDev\Test\FeatureTestCase;

/**
 * @internal
 *
 * @covers \AftDev\ServiceManager\ConfigProvider
 * @covers \AftDev\ServiceManager\Resolver\ResolverFactory
 */
class ResolverTest extends FeatureTestCase
{
    /**
     * Test that the service get created properly.
     */
    public function testService()
    {
        $resolver = $this->container->get(Resolver::class);

        $this->assertInstanceOf(Resolver::class, $resolver);
    }
}
