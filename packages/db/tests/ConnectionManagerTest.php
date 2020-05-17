<?php

namespace AftDevTest\Db;

use AftDev\Db\ConnectionInterface;
use AftDev\Db\ConnectionManager;
use AftDev\Test\TestCase;

/**
 * @internal
 * @covers \AftDev\Db\ConnectionManager
 */
class ConnectionManagerTest extends TestCase
{
    public function testConnections()
    {
        $connections = [
            'mysql' => [
                'hostname' => 'hostname',
                'default' => true,
            ],
            'second_connection' => [
                'hostname' => 'postgre',
            ],
        ];

        $connectionManager = new ConnectionManager($connections);

        $connection = $connectionManager->connection();
        $default = $connectionManager->getDefault();

        $secondConnection = $connectionManager->connection('second_connection');

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
        $this->assertSame($default, $connection);
        $this->assertNotSame($connection, $secondConnection);

        $this->assertSameSize($connections, $connectionManager->getAll());
    }

    public function testInvalidConnection()
    {
        $connectionManager = new ConnectionManager([
            'a' => [
                'hostname' => 'a',
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $connectionManager->connection('invalid');
    }
}
