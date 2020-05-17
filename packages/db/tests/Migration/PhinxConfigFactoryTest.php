<?php

namespace AftDevTest\Db\Migration;

use AftDev\Db\ConfigProvider;
use AftDev\Db\Connection;
use AftDev\Db\ConnectionManager;
use AftDev\Db\Migration\PhinxConfigFactory;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Db\Migration\PhinxConfigFactory
 */
class PhinxConfigFactoryTest extends TestCase
{
    /**
     * Make sure the factory properly transforms our config to a Phinx Config.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $connectionManager = $this->prophesize(ConnectionManager::class);

        $container->get(ConnectionManager::class)->willReturn($connectionManager->reveal());

        $configProvider = new ConfigProvider();
        $container->get('config')->willReturn([
            ConfigProvider::KEY_DATABASE => [
                'migrations' => $configProvider->getMigrationConfig(),
            ],
        ]);

        $connectionManager->getAll()->willReturn([
            'mysql' => new Connection([
                'type' => 'mysql',
                'hostname' => 'HOSTA',
                'default' => false,
            ]),
            'postgresql' => new Connection([
                'type' => 'sql',
                'hostname' => 'postgresql',
                'default' => false,
            ]),
            'sql' => new Connection([
                'type' => 'sql',
                'hostname' => 'sql',
                'default' => false,
            ]),
            'sqlsrv' => new Connection([
                'type' => 'sqlsrv',
                'hostname' => 'sqlsrv',
                'default' => true,
            ]),
            'sqlite' => new Connection([
                'type' => 'sqlite',
                'hostname' => 'sqlite',
                'default' => false,
            ]),
        ]);

        $factory = new PhinxConfigFactory();

        $config = $factory($container->reveal());

        $this->assertSame($configProvider->getMigrationConfig()['paths'], $config->getMigrationPaths());
        $this->assertSame($configProvider->getMigrationConfig()['seeds'], $config->getSeedPaths());
        $this->assertSame('sqlsrv', $config->getDefaultEnvironment());
        $this->assertSame('HOSTA', $config->getEnvironment('mysql')['host']);
    }
}
