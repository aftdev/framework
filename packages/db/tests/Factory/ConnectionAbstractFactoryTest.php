<?php

namespace AftDevTest\Db\Factory;

use AftDev\Db\Connection;
use AftDev\Db\Factory\ConnectionAbstractFactory;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Db\Factory\ConnectionAbstractFactory
 */
class ConnectionAbstractFactoryTest extends TestCase
{
    /**
     * Test factory.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $options = [
            'hostname' => 'test db',
        ];

        $factory = new ConnectionAbstractFactory();

        $this->assertTrue($factory->canCreate($container->reveal(), 'test'));

        $connection = $factory($container->reveal(), 'connection', $options);
        $this->assertInstanceOf(Connection::class, $connection);
    }
}
