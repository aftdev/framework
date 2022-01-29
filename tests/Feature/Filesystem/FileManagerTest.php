<?php

namespace AftDev\Test\Feature\Filesystem;

use AftDev\Filesystem\FileManager;
use AftDev\Test\FeatureTestCase;

/**
 * @internal
 *
 * @covers \AftDev\Filesystem\Factory\FileManagerFactory
 * @covers \AftDev\Filesystem\FileManager
 */
final class FileManagerTest extends FeatureTestCase
{
    public function testFileManager()
    {
        /** @var FileManager $fileManager */
        $fileManager = $this->container->get(FileManager::class);

        foreach (['s3', 'local'] as $disk) {
            $content = $fileManager->read($disk.'://test.txt');
            $this->assertEquals('testFile '.$disk, trim($content));
        }
    }
}
