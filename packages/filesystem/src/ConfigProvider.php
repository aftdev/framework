<?php

namespace AftDev\Filesystem;

use AftDev\Filesystem\Factory\DiskAbstractFactory;

class ConfigProvider
{
    public const CONFIG_KEY = 'filesystem';

    public function __invoke(): array
    {
        $config['dependencies'] = $this->getDependencyConfig();
        $config[self::CONFIG_KEY] = [
            'disks' => $this->getDiskManagerConfig(),
            'plugin_manager' => $this->getPluginManagerConfig(),
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
            ],
            'factories' => [
                FileManager::class => Factory\FileManagerFactory::class,
                DiskManager::class => Factory\DiskManagerFactory::class,
                PluginManager::class => Factory\PluginManagerFactory::class,
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
                'visibility' => 'public',
                'case_sensitive' => true,
                'disable_asserts' => false,
            ],
            'plugins' => [
                'local' => [
                    'root' => 'data/files',
                ],
                's3' => [
                    'case_sensitive' => true,
                    'disable_asserts' => true,
                    'visibility' => 'private',
                    'credentials' => [
                        'key' => 'your-key',
                        'secret' => 'your-secret',
                    ],
                    'region' => 'your-region',
                    'version' => 'latest',
                ],
                'test' => [
                    'adapter' => 'null',
                    'options' => [],
                ],
            ],
            'abstract_factories' => [
                'default' => DiskAbstractFactory::class,
            ],
        ];
    }

    /**
     * Get configuration for the disk service manager.
     */
    public function getPluginManagerConfig(): array
    {
        return [
        ];
    }
}
