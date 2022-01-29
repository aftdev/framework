# Filesystem Manager.

Filesystem based on [Flysystem](https://flysystem.thephpleague.com/v2/docs/)

## FileManager

This FileManager is actually a flysystem
[MountManager](https://flysystem.thephpleague.com/v2/docs/advanced/mount-manager/).

All the disks registered in the disk manager will be available

### Usage:

See https://flysystem.thephpleague.com/v2/docs/advanced/mount-manager/

````php

```php
class X {
    __construct(FileManager $fileManager) {
        $contents = $manager->read('local://some/file.txt');
        $contents = $manager->read('s3://some/file.txt');
    }
}
````

## DiskManager

This is the manager than handle all your disks (Filesystem). You can use the
diskmanager directly but ideally you would only interact with the FileManager.

### Config

This is the default configuration. A `local` and a `s3` file system are defined.

```php
return [
    'filesystem' => [
        'disks' => [
            'default' => 'local',
            'cloud' => 's3',
            'default_options' => [
                Config::OPTION_VISIBILITY => Visibility::PUBLIC,
                Config::OPTION_DIRECTORY_VISIBILITY => Visibility::PUBLIC,
            ],
            'plugins' => [
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
        ],
    ],
];
```

### Available disk types.

- `local` -> `League\Flysystem\Local\LocalFilesystemAdapter`
- `file` -> alias to local
- `s3` `League\Flysystem\AwsS3V3\AwsS3V3Adapter` (requires
  `league/flysystem-aws-s3-v3`)
- `memory` -> `League\Flysystem\InMemory\InMemoryFilesystemAdapter` (usually
  used for testing)

### Usage

```php
class X {
    __construct(DiskManager $diskManager) {

        // Use default disk.
        $diskManager->default()->read('path/to/file');

        // Use cloud disk.
        $diskManager->cloud()->read('path/to/file');

        // Use specific disk
        $diskManager->disk('another')->read('path/to/file');
    }
}
```

### Custom Adapters.

The disk manager is a Lamninas Service Manager so you can add your own factories
and aliases.

```php
return [
    'filesystem' => [
        'disks' => [
            'factories' => [
                CustomFilesystem::class => Factory::class,
            ],
            'aliases' => [
                'customAdapter' => CustomAdapter::class,
            ],
            'plugins' => [
                'customAdapter' => [],
            ],
        ],
    ],
];
```
