<?php

namespace AftDev\Test\Feature\Filesystem;

use AftDev\Filesystem\DiskManager;
use AftDev\Test\FeatureTestCase;

/**
 * @internal
 *
 * @covers \AftDev\Filesystem\DiskManager
 */
final class DisksTest extends FeatureTestCase
{
    /**
     * Test Disks.
     *
     * Each disk type should be setup with a test.txt file.
     *
     * @dataProvider diskProvider
     *
     * @param mixed $disk
     */
    public function testDisks($disk)
    {
        $diskManager = $this->container->get(DiskManager::class);
        $disk = $diskManager->disk($disk);

        $this->assertStringContainsString('testFile', $disk->read('test.txt'));
    }

    static public function diskProvider(): array
    {
        return [
            'local' => ['local'],
            's3' => ['s3'],
        ];
    }
}
