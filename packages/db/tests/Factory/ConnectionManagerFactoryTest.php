<?php

namespace AftDevTest\Db\Factory;

use AftDev\Db\ConfigProvider;
use AftDev\Db\Factory\ConnectionManagerFactory;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Db\Factory\ConnectionManagerFactory
 */
class ConnectionManagerFactoryTest extends TestCase
{
    /**
     * Test factory.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $connnections = [
            'mysql' => [
                'default' => true,
                'hostname' => 'test',
            ],
        ];

        $container->get('config')->willReturn([
            ConfigProvider::KEY_DATABASE => [
                'connections' => $connnections,
            ],
        ]);

        $factory = new ConnectionManagerFactory();
        $manager = $factory($container->reveal());

        $this->assertSameSize($connnections, $manager->getAll());
    }
}
