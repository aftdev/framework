<?php

namespace AftDevTest\DbEloquent\Capsule;

use AftDev\Db\Connection;
use AftDev\Db\ConnectionManager;
use AftDev\DbEloquent\Capsule\CapsuleManager;
use AftDev\DbEloquent\Capsule\CapsuleManagerFactory;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\DbEloquent\Capsule\CapsuleManagerFactory
 */
class CapsuleManagerFactoryTest extends TestCase
{
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $connectionManager = $this->prophesize(ConnectionManager::class);

        $container->get(ConnectionManager::class)->willReturn($connectionManager->reveal());

        $connectionManager->getAll()->willReturn([
            'testA' => new Connection(
                [
                    'type' => 'mysql',
                    'hostname' => 'host1',
                    'username' => 'username',
                    'password' => 'password',
                    'database' => 'db1',
                ]
            ),
        ]);

        $factory = new CapsuleManagerFactory();
        $manager = $factory($container->reveal());

        $this->assertInstanceOf(CapsuleManager::class, $manager);
    }
}
