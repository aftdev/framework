<?php

namespace AftDevTest\Db;

use AftDev\Db\Connection;
use AftDev\Test\TestCase;

/**
 * @internal
 * @covers \AftDev\Db\Connection
 */
class ConnectionTest extends TestCase
{
    /**
     * Test config provider.
     */
    public function testConfigProvider()
    {
        $options = [
            'default' => true,
            'debug' => true,
            'type' => 'mysql',
            'database' => 'db_name',
            'hostname' => 'db_host',
            'username' => 'db_username',
            'password' => 'db_password',
            'port' => 3306,
            'charset' => 'db_charset',
            'collation' => 'db_collation',
            'extra' => [
                'a' => 'b',
            ],
        ];

        $connection = new Connection($options);

        foreach ($options as $optionKey => $optionValue) {
            $this->assertEquals($optionValue, $connection->{$optionKey});
        }
    }
}
