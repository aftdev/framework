<?php

namespace AftDev\Test\Feature\DbEloquent;

use AftDev\Test\FeatureTestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;

/**
 * @internal
 * @covers \AftDev\DbEloquent\Factory\DefaultConnectionFactory
 */
class DefaultConnectionTest extends FeatureTestCase
{
    public function testDbBuilder()
    {
        $connection = $this->container->get(ConnectionInterface::class);

        $this->assertInstanceOf(Connection::class, $connection);
    }
}
