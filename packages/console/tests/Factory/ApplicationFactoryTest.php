<?php

namespace AftDevTest\Console\Factory;

use AftDev\Console\CommandManager;
use AftDev\Console\ConfigProvider;
use AftDev\Console\Factory\ApplicationFactory;
use AftDev\Test\TestCase;
use AftDevTest\Console\Command as TestCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/**
 * Class CommandManagerFactoryTest.
 *
 * @internal
 *
 * @covers \AftDev\Console\Factory\ApplicationFactory
 */
class ApplicationFactoryTest extends TestCase
{
    /**
     * Test that factory return properly loaded manager.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $commandManager = $this->prophesize(CommandManager::class);

        $config = [
            ConfigProvider::KEY_CONSOLE => [
                // Define list of commands.
                ConfigProvider::KEY_COMMANDS => [
                    TestCommand::getDefaultName() => TestCommand::class,
                ],
            ],
        ];

        $container
            ->get('config')
            ->shouldBeCalledTimes(1)
            ->willReturn($config)
        ;

        $container
            ->get(CommandManager::class)
            ->shouldBeCalledTimes(1)
            ->willReturn($commandManager->reveal())
        ;

        $commandManager
            ->has(TestCommand::class)
            ->shouldBeCalled()
            ->willReturn(true)
        ;

        $commandManager
            ->get(TestCommand::class)
            ->shouldBeCalledTimes(1)
            ->willReturn(new TestCommand())
        ;

        $application = (new ApplicationFactory())($container->reveal());

        $this->assertInstanceOf(Application::class, $application);

        // Make sure the command loader is properly loaded.
        $this->assertTrue($application->has(TestCommand::getDefaultName()));
    }
}
