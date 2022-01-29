<?php

namespace AftDevTest\Filesystem;

use AftDev\Filesystem\ConfigProvider;
use AftDev\Test\TestCase;

/**
 * @internal
 * @covers \AftDev\Filesystem\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    /**
     * Test Config provider.
     */
    public function testConfig()
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider();
        $this->assertArrayHasKey('dependencies', $config);

        $filesystemConfig = $config[ConfigProvider::CONFIG_KEY];

        $this->assertArrayHasKey('disks', $filesystemConfig);
    }
}
