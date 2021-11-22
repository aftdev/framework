<?php

namespace AftDevTest\Db;

use AftDev\Db\ConfigProvider;
use AftDev\Test\TestCase;

/**
 * @internal
 * @covers \AftDev\Db\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    /**
     * Test config provider.
     */
    public function testConfigProvider()
    {
        $provider = new ConfigProvider();

        $config = $provider();

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey(ConfigProvider::KEY_DATABASE, $config);

        $this->assertArrayHasKey('connections', $config[ConfigProvider::KEY_DATABASE]);
        $this->assertArrayHasKey('migrations', $config[ConfigProvider::KEY_DATABASE]);
    }
}
