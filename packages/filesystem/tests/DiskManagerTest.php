<?php

namespace AftDevTest\Filesystem;

use AftDev\Filesystem\DiskManager;
use AftDev\Filesystem\Factory\DiskAbstractFactory;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\MimeTypeDetection\MimeTypeDetector;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\DiskManager
 * @covers \AftDev\Filesystem\Factory\DiskAbstractFactory
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
                    'service' => InMemoryFilesystemAdapter::class,
                ],
                'cloud_disk' => [
                    'service' => InMemoryFilesystemAdapter::class,
                    'options' => ['b' => 'b'],
                ],
            ],
            'abstract_factories' => [
                'default' => DiskAbstractFactory::class,
            ],
        ];

        $diskManager = new DiskManager($container->reveal(), $managerConfig);

        $container->get(DiskManager::class)->willReturn($diskManager);
        $container->has(MimeTypeDetector::class)->willReturn(false);

        $privateAdapter = new \ReflectionProperty(Filesystem::class, 'adapter');
        $privateAdapter->setAccessible(true);

        // Test named disk.
        $disk = $diskManager->disk('disk_1');

        $this->assertInstanceOf(Filesystem::class, $disk);
        $this->assertInstanceOf(InMemoryFilesystemAdapter::class, $privateAdapter->getValue($disk));

        // Test cloud.
        $cloud = $diskManager->cloud();
        $this->assertInstanceOf(Filesystem::class, $cloud);
        $this->assertInstanceOf(InMemoryFilesystemAdapter::class, $privateAdapter->getValue($cloud));

        // Test default.
        $default = $diskManager->disk();
        $this->assertSame($disk, $default);
    }
}
