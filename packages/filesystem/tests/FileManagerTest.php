<?php

namespace AftDevTest\Filesystem;

use AftDev\Filesystem\DiskManager;
use AftDev\Filesystem\FileManager;
use AftDev\Filesystem\PluginManager;
use AftDev\Test\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Plugin\AbstractPlugin;
use Prophecy\Exception\Doubler\MethodNotFoundException;

/**
 * @internal
 * @covers \AftDev\Filesystem\FileManager
 */
class FileManagerTest extends TestCase
{
    /**
     * Test the filemanager disk and plugin autodiscovery.
     */
    public function testAutoDiscovery()
    {
        $diskManager = $this->prophesize(DiskManager::class);
        $pluginManager = $this->prophesize(PluginManager::class);

        $config = [];
        $filesystem = new FileManager($config, $diskManager->reveal(), $pluginManager->reveal());

        $this->assertSame($diskManager->reveal(), $filesystem->getDiskManager());

        $filesystemMock = $this->prophesize(Filesystem::class);

        $diskManager->hasPlugin('disk1')->willReturn(true);
        $diskManager->disk('disk1')->willReturn($filesystemMock->reveal());

        $disk = $filesystem->getFilesystem('disk1');
        $this->assertInstanceOf(FilesystemInterface::class, $disk);

        // Test plugin manager.
        $pluginManager->has('testPluginFromManager')->shouldBeCalledOnce()->willReturn(true);

        $plugin = $this->getMockBuilder(AbstractPlugin::class)
            ->addMethods(['handle'])
            ->getMockForAbstractClass()
        ;

        $path = '/path-of-file';
        $expectedArguments = ['a', 'b'];

        $testReturn = 'test';

        $plugin->expects($this->exactly(1))
            ->method('handle')
            ->with($path, $expectedArguments[0], $expectedArguments[1])
            ->willReturn($testReturn)
        ;

        $pluginManager->get('testPluginFromManager')->shouldBeCalledOnce()->willReturn($plugin);

        $test = $filesystem->testPluginFromManager('disk1://'.$path, $expectedArguments[0], $expectedArguments[1]);

        $this->assertSame($testReturn, $test);

        // Test exception now.
        $pluginManager->has('totallyInvalidPlugin')->willReturn(false);
        // Function name that dont match a plugin are passed directly to the file system.
        // So we need to check for MethodNotFoundException exception.
        $this->expectException(MethodNotFoundException::class);

        $filesystem->totallyInvalidPlugin('disk1://'.$path);
    }
}
