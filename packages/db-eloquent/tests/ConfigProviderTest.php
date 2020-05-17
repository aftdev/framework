<?php

namespace AftDevTest\DbEloquent;

use AftDev\DbEloquent\ConfigProvider;
use AftDev\Test\TestCase;

/**
 * Class ConfigProviderTest.
 *
 * @covers \AftDev\DbEloquent\ConfigProvider
 *
 * @internal
 */
class ConfigProviderTest extends TestCase
{
    public function testConfig()
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider();

        $this->assertArrayHasKey('dependencies', $config);
    }
}
