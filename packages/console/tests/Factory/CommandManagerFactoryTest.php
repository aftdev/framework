<?php

namespace AftDevTest\Console\Factory;

use AftDev\Console\CommandManager;
use AftDev\Console\ConfigProvider;
use AftDev\Console\Factory\CommandManagerFactory;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Class CommandManagerFactoryTest.
 *
 * @internal
 * @covers \AftDev\Console\Factory\CommandManagerFactory
 */
class CommandManagerFactoryTest extends TestCase
{
    /**
     * Test that factory return properly loaded manager.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $config = [
            ConfigProvider::KEY_CONSOLE => [
                ConfigProvider::KEY_COMMAND_MANAGER => [
                    'factories' => [
                        'test' => 'test',
                    ],
                ],
            ],
        ];

        $container
            ->get('config')
            ->shouldBeCalledTimes(2)
            ->willReturn($config, [])
        ;

        $factory = new CommandManagerFactory();

        $commandManager = $factory($container->reveal());

        $this->assertInstanceOf(CommandManager::class, $commandManager);
        $this->assertTrue($commandManager->has('test'));

        $commandManager = $factory($container->reveal());

        $this->assertInstanceOf(CommandManager::class, $commandManager);
        $this->assertFalse($commandManager->has('test'));
    }
}
