<?php

namespace AftDev\Filesystem\Factory;

use AftDev\ServiceManager\Factory\ReflectionAbstractFactory;
use Aws\S3\S3Client;
use Illuminate\Support\Arr;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;

class S3AdapterFactory extends ReflectionAbstractFactory
{
    use GetConfigTrait;

    public const S3_CLIENT_CONFIG = [
        'endpoint',
        'credentials',
        'version',
        'region',
        'use_path_style_endpoint',
    ];

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Fetch config of the filesystem.
        [$filesystemConfig, $adapterConfig] = $this->getConfigFromOptions($options ?? []);

        // Assign default values if not set properly.
        $adapterConfig['bucket'] ??= 'default';
        $adapterConfig['prefix'] ??= '';

        $clientClass = $adapterConfig['client'] ?? null;
        // Use S3 Client from container or build one from the options.
        if ($clientClass) {
            $adapterConfig['client'] = $container->get($clientClass);
        } else {
            $s3ClientConfig = Arr::only($adapterConfig, self::S3_CLIENT_CONFIG);
            $adapterConfig['client'] = new S3Client($s3ClientConfig);
        }

        Arr::forget($adapterConfig, self::S3_CLIENT_CONFIG);

        // Build adapter.
        $adapter = parent::__invoke($container, $requestedName, $adapterConfig);

        // Return a filesystem.
        return new Filesystem($adapter, $filesystemConfig);
    }
}
