<?php

namespace AftDevTest\Filesystem\Factory;

use AftDev\Filesystem\Factory\S3AdapterFactory;
use AftDev\Test\TestCase;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\Factory\S3AdapterFactory
 */
class S3AdapterFactoryTest extends TestCase
{
    /**
     * Test that the factory use the right information.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $clientFromOptions = [
            'region' => 'us-east-1',
            'version' => 'latest',
            'endpoint' => 'http://endpoint',
            'bucket_endpoint' => true,
            'use_path_style_endpoint' => true,
        ];
        $factory = new S3AdapterFactory();

        /** @var \League\Flysystem\Filesystem $s3FileSystem */
        $s3FileSystem = $factory($container->reveal(), AwsS3V3Adapter::class, $clientFromOptions);

        $this->assertInstanceOf(Filesystem::class, $s3FileSystem);
    }

    public function testFactoryWithS3ClientFromContainer()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(Argument::any())->willReturn(false);

        $s3Client = $this->prophesize(S3Client::class);

        $container->get('S3ClientFromContainer')->willReturn($s3Client->reveal());

        $clientFromOptions = [
            'client' => 'S3ClientFromContainer',
            'bucket_endpoint' => true,
            'use_path_style_endpoint' => true,
        ];
        $factory = new S3AdapterFactory();

        /** @var \League\Flysystem\Filesystem $s3FileSystem */
        $s3FileSystem = $factory($container->reveal(), AwsS3V3Adapter::class, $clientFromOptions);

        $this->assertInstanceOf(Filesystem::class, $s3FileSystem);
    }
}
