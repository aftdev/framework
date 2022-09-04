<?php

namespace AftDevTest\Cache;

use AftDev\Cache\ConfigProvider;
use AftDev\Test\TestCase;

/**
 * @internal
 *
 * @covers \AftDev\Cache\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    public function testConfig()
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider();

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey('cache', $config);
    }
}
