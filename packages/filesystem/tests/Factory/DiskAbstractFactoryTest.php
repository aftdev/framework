<?php

namespace AftDevTest\Filesystem\Factory;

use AftDev\Filesystem\Factory\DiskAbstractFactory;
use AftDev\Test\TestCase;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Visibility;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\Factory\DiskAbstractFactory
 * @covers \AftDev\Filesystem\Factory\GetConfigTrait
 */
class DiskAbstractFactoryTest extends TestCase
{
    /**
     * Test that the factory use the right information.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has(Argument::any())->willReturn(false);

        $options = [
            Config::OPTION_VISIBILITY => Visibility::PRIVATE,
            Config::OPTION_DIRECTORY_VISIBILITY => Visibility::PUBLIC,
            'extraOptionsA' => 'test',
            'extraOptionsB' => 'test2',
        ];

        $factory = new DiskAbstractFactory();

        $disk = $factory($container->reveal(), TestAdapter::class, $options);

        $r = new \ReflectionObject($disk);
        $privateConfig = $r->getProperty('config');
        $privateConfig->setAccessible(true);

        $this->assertInstanceOf(Filesystem::class, $disk);
        $diskConfig = $privateConfig->getValue($disk);

        // Test that all the configurations were set properly.
        $this->assertSame($options[Config::OPTION_VISIBILITY], $diskConfig->get(Config::OPTION_VISIBILITY));
        $this->assertSame($options[Config::OPTION_DIRECTORY_VISIBILITY], $diskConfig->get(Config::OPTION_DIRECTORY_VISIBILITY));

        // Make sure the adapter got the right options as well.
        $privateAdapter = $r->getProperty('adapter');
        $privateAdapter->setAccessible(true);

        $adapter = $privateAdapter->getValue($disk);
        $this->assertSame($options['extraOptionsA'], $adapter->extraOptionsA);
        $this->assertSame($options['extraOptionsB'], $adapter->extraOptionsB);
        $this->assertSame('notset', $adapter->visibility);
    }
}

class TestAdapter extends InMemoryFilesystemAdapter
{
    public $extraOptionsA;
    public $extraOptionsB;
    public $visibility;

    public function __construct($extraOptionsA, $extraOptionsB, $visibility = 'notset')
    {
        $this->extraOptionsA = $extraOptionsA;
        $this->extraOptionsB = $extraOptionsB;
        $this->visibility = $visibility;
    }
}
