<?php

namespace AftDevTest\Filesystem;

use AftDev\Filesystem\DiskManager;
use AftDev\Filesystem\Factory\DiskAbstractFactory;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\DiskManager
 */
class DiskManagerTest extends TestCase
{
    public function testDisks()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(Resolver::class)->willReturn(false);

        $managerConfig = [
            'default' => 'disk_1',
            'cloud' => 'cloud_disk',
            'plugins' => [
                'disk_1' => [
                    'service' => NullAdapter::class,
                ],
                'cloud_disk' => [
                    'service' => NullAdapter::class,
                    'options' => ['b' => 'b'],
                ],
            ],
            'abstract_factories' => [
                'default' => DiskAbstractFactory::class,
            ],
        ];

        $diskManager = new DiskManager($container->reveal(), $managerConfig);

        $container->get(DiskManager::class)->willReturn($diskManager);

        // Test named disk.
        $disk = $diskManager->disk('disk_1');
        $this->assertInstanceOf(Filesystem::class, $disk);
        $this->assertInstanceOf(NullAdapter::class, $disk->getAdapter());

        // Test cloud.
        $cloud = $diskManager->cloud();
        $this->assertInstanceOf(Filesystem::class, $cloud);
        $this->assertInstanceOf(NullAdapter::class, $cloud->getAdapter());

        // Test default.
        $default = $diskManager->disk();
        $this->assertSame($disk, $default);
    }
}
