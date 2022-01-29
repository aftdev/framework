<?php

namespace AftDev\Filesystem;

use AftDev\Filesystem\Factory\S3AdapterFactory;
use AftDev\ServiceManager\AbstractManager;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Class DiskManager.
 */
class DiskManager extends AbstractManager
{
    protected $instanceOf = FilesystemOperator::class;

    protected $aliases = [
        'memory' => InMemoryFilesystemAdapter::class,
        'local' => LocalFilesystemAdapter::class,
        'file' => 'local',
        's3' => AwsS3V3Adapter::class,
    ];

    protected $factories = [
        AwsS3V3Adapter::class => S3AdapterFactory::class,
    ];

    /**
     * Disk to be used for "cloud" operation.
     *
     * @var string
     */
    protected $cloudDisk;

    /**
     * Get disk by name.
     */
    public function disk(string $diskName = null): FilesystemOperator
    {
        return null === $diskName ? $this->getDefault() : $this->getPlugin($diskName);
    }

    /**
     * Get the cloud disk.
     *
     * If no cloud disk has been configured it will return the default disk.
     */
    public function cloud(): FilesystemOperator
    {
        return $this->cloudDisk ? $this->disk($this->cloudDisk) : $this->disk();
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $config)
    {
        parent::configure($config);

        if (isset($config['cloud'])) {
            $this->cloudDisk = $config['cloud'];
        }

        return $this;
    }

    /**
     * Create and retrieve all disks.
     *
     * @return array
     */
    public function getAllDisks()
    {
        $disks = [];
        foreach ($this->pluginsOptions as $diskName => $diskConfig) {
            $disks[$diskName] = $this->getPlugin($diskName);
        }

        return $disks;
    }
}
