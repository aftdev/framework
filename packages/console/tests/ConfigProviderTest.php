<?php

namespace AftDevTest\Console;

use AftDev\Console\ConfigProvider;
use AftDev\Test\TestCase;

/**
 * Class ConfigProviderTest.
 *
 * @internal
 * @covers \AftDev\Console\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    /**
     * Test Config provider.
     */
    public function testInvoke()
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider();
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey(ConfigProvider::KEY_CONSOLE, $config);

        $consoleConfig = $config[ConfigProvider::KEY_CONSOLE];

        $this->assertArrayHasKey(ConfigProvider::KEY_COMMANDS, $consoleConfig);
        $this->assertArrayHasKey(ConfigProvider::KEY_COMMAND_MANAGER, $consoleConfig);
    }
}
