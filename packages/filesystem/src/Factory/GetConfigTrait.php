<?php

namespace AftDev\Filesystem\Factory;

use League\Flysystem\Config;
use League\Flysystem\Visibility;

trait GetConfigTrait
{
    /**
     * Return the filesystem and adapter configs.
     */
    protected function getConfigFromOptions(array $options): array
    {
        $filesystemConfig = [
            Config::OPTION_VISIBILITY => Visibility::PUBLIC,
            Config::OPTION_DIRECTORY_VISIBILITY => Visibility::PUBLIC,
        ];

        // Fetch config of the filesystem.
        $filesystemConfig = array_intersect_key($options + $filesystemConfig, $filesystemConfig);

        // The rest are config for the adapter itself.
        $adapterConfig = array_diff_key($options, $filesystemConfig);

        return [$filesystemConfig, $adapterConfig];
    }
}
