<?php

namespace AftDev\Filesystem;

use AftDev\Filesystem\Factory\DiskAbstractFactory;
use League\Flysystem\Config;
use League\Flysystem\MountManager;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;

class ConfigProvider
{
    public const CONFIG_KEY = 'filesystem';

    public function __invoke(): array
    {
        $config['dependencies'] = $this->getDependencyConfig();
        $config[self::CONFIG_KEY] = [
            'disks' => $this->getDiskManagerConfig(),
        ];

        return $config;
    }

    /**
     * Services provided by this package.
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases' => [
                'FileManager' => FileManager::class,
                MountManager::class => FileManager::class,
            ],
            'invokables' => [
                MimeTypeDetector::class => FinfoMimeTypeDetector::class,
            ],
            'factories' => [
                FileManager::class => Factory\FileManagerFactory::class,
                DiskManager::class => Factory\DiskManagerFactory::class,
            ],
        ];
    }

    /**
     * Get configuration for the disk service manager.
     */
    public function getDiskManagerConfig(): array
    {
        return [
            'default' => 'local',
            'cloud' => 's3',
            'default_options' => [
                Config::OPTION_VISIBILITY => Visibility::PUBLIC,
                Config::OPTION_DIRECTORY_VISIBILITY => Visibility::PUBLIC,
            ],
            'plugins' => [
                'memory' => [],
                'local' => [
                    'location' => 'data/files',
                ],
                's3' => [
                    'bucket' => 'default',
                    'prefix' => '',
                    'credentials' => [
                        'key' => 'your-key',
                        'secret' => 'your-secret',
                    ],
                    'region' => 'your-region',
                    'version' => 'latest',
                ],
            ],
            'abstract_factories' => [
                'default' => DiskAbstractFactory::class,
            ],
        ];
    }
}
