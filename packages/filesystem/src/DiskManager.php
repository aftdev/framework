<?php

namespace AftDev\Filesystem;

use AftDev\ServiceManager\AbstractManager;
use League\Flysystem\Adapter;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Memory\MemoryAdapter;
use Spatie\FlysystemDropbox\DropboxAdapter;

/**
 * Class DiskManager.
 */
class DiskManager extends AbstractManager
{
    protected $instanceOf = FilesystemInterface::class;

    protected $aliases = [
        'null' => Adapter\NullAdapter::class,
        'local' => Adapter\Local::class,
        'file' => 'local',
        'ftp' => Adapter\Ftp::class,
        'memory' => MemoryAdapter::class,
        'dropbox' => DropboxAdapter::class,
        's3' => AwsS3Adapter::class,
    ];

    /**
     * Disk to be used for "cloud" operation.
     *
     * @var string
     */
    protected $cloudDisk;

    /**
     * Get disk by name.
     *
     * @param null|mixed $diskName
     */
    public function disk($diskName = null): FilesystemInterface
    {
        return null === $diskName ? $this->getDefault() : $this->getPlugin($diskName);
    }

    /**
     * Get the cloud disk.
     *
     * If no cloud disk has been configured it will return the default disk.
     */
    public function cloud(): FilesystemInterface
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
}
