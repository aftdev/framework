<?php

namespace AftDevTest\Cache\Factory;

use AftDev\Cache\Factory\RedisAdapterFactory;
use AftDev\Test\TestCase;

/**
 * @internal
 *
 * @covers \AftDev\Cache\Factory\RedisAdapterFactory
 */
class RedisAdapterFactoryTest extends TestCase
{
    /**
     * Test the factory connection options.
     *
     * @dataProvider optionProviders
     *
     * @param mixed $expected
     */
    public function testConnectionOptions(array $options = [], $expected = [])
    {
        $factory = new RedisAdapterFactory();

        [$servers, $options] = $factory->getConnectionOptions($options);

        $this->assertSame($expected[0], $servers);
        $this->assertSame($expected[1], $options);
    }

    /**
     * Data provider for the memcached factory.
     *
     * @return array
     */
    static public function optionProviders()
    {
        return [
            'using servers' => [
                'options' => [
                    'servers' => ['redis:XXXX', 'redis:YYYY'],
                ],
                'expected' => [
                    ['redis:XXXX', 'redis:YYYY'],
                    [],
                ],
            ],
            'using server' => [
                'options' => [
                    'server' => 'redis:XXXX',
                ],
                'expected' => [
                    'redis:XXXX',
                    [],
                ],
            ],
            'using redis_options' => [
                'options' => [
                    'server' => 'redis:XXXX',
                    'redis_options' => [
                        'compression' => true,
                        'lazy' => false,
                        'persistent' => 0,
                    ],
                ],
                'expected' => [
                    'redis:XXXX',
                    [
                        'compression' => true,
                        'lazy' => false,
                        'persistent' => 0,
                    ],
                ],
            ],
            'using options' => [
                'options' => [
                    'server' => 'redis:XXXX',
                    'options' => [
                        'compression' => true,
                        'lazy' => false,
                        'persistent' => 0,
                    ],
                ],
                'expected' => [
                    'redis:XXXX',
                    [
                        'compression' => true,
                        'lazy' => false,
                        'persistent' => 0,
                    ],
                ],
            ],
        ];
    }
}
