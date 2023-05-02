<?php

namespace AftDevTest\Cache\Factory;

use AftDev\Cache\Factory\MemcachedAdapterFactory;
use AftDev\Test\TestCase;

/**
 * @internal
 *
 * @covers \AftDev\Cache\Factory\MemcachedAdapterFactory
 */
class MemcachedAdapterFactoryTest extends TestCase
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
        $factory = new MemcachedAdapterFactory();

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
                    'servers' => ['memcached:XXXX', 'memcached:YYYY'],
                ],
                'expected' => [
                    ['memcached:XXXX', 'memcached:YYYY'],
                    [],
                ],
            ],
            'using server' => [
                'options' => [
                    'server' => 'memcached:XXXX',
                ],
                'expected' => [
                    'memcached:XXXX',
                    [],
                ],
            ],
            'using memcached_options' => [
                'options' => [
                    'server' => 'memcached:XXXX',
                    'memcached_options' => [
                        'compression' => true,
                        'libketama_compatible' => true,
                        'serializer' => 'igbinary',
                    ],
                ],
                'expected' => [
                    'memcached:XXXX',
                    [
                        'compression' => true,
                        'libketama_compatible' => true,
                        'serializer' => 'igbinary',
                    ],
                ],
            ],
            'using options' => [
                'options' => [
                    'server' => 'memcached:XXXX',
                    'options' => [
                        'compression' => true,
                        'libketama_compatible' => true,
                        'serializer' => 'igbinary',
                    ],
                ],
                'expected' => [
                    'memcached:XXXX',
                    [
                        'compression' => true,
                        'libketama_compatible' => true,
                        'serializer' => 'igbinary',
                    ],
                ],
            ],
        ];
    }
}
