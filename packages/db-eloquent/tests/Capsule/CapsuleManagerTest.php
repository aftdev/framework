<?php

namespace AftDevTest\DbEloquent\Capsule;

use AftDev\Db\Connection;
use AftDev\DbEloquent\Capsule\CapsuleManager;
use AftDev\Test\TestCase;
use Illuminate\Container\Container;

/**
 * @internal
 * @covers \AftDev\DbEloquent\Capsule\CapsuleManager
 */
class CapsuleManagerTest extends TestCase
{
    public function testConnections()
    {
        $container = new Container();

        $capsuleManager = new CapsuleManager($container);

        $connections = [
            'testA' => new Connection(
                [
                    'type' => 'mysql',
                    'hostname' => 'host1',
                    'username' => 'username',
                    'password' => 'password',
                    'database' => 'db1',
                    'port' => 3307,
                    'extra' => [
                        'sticky' => true,
                        'read' => [
                            'host' => ['read-host1'],
                        ],
                        'write' => [
                            'host' => ['write-host1'],
                        ],
                    ],
                ]
            ),
            'testB' => new Connection(
                [
                    'type' => 'sqlite',
                    'hostname' => 'host2',
                    'database' => 'db2',
                    'default' => true,
                ]
            ),
        ];

        $capsuleManager->setConnections($connections);

        $connectionA = $capsuleManager->getConnection('testA');

        $this->assertEquals([
            'name' => 'testA',
            'driver' => 'mysql',
            'database' => 'db1',
            'host' => ['write-host1'],
            'username' => 'username',
            'password' => 'password',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'sticky' => true,
            'port' => 3307,
        ], $connectionA->getConfig());

        $connectionB = $capsuleManager->getConnection('testB');

        $this->assertNotEquals($connectionA->getConfig(), $connectionB->getConfig());

        $defaultConnection = $capsuleManager->getConnection();
        $this->assertSame($connectionB, $defaultConnection);
    }
}
