# Filesystem Manager.

Filesystem based on Flysystem https://flysystem.thephpleague.com/docs/

## FileManager

This service is actually an improved flysystem MountManager.

It will automatically mount any disks from the Disk Manager and plugins from the Plugin Manager

### Config

Default configuration is provided by the Config Provider.

```
return [
    'filesystem' => [
        'disks' => [], <- disk configuration
        'disk_manager => [], <-- Configuration for the disk manager
        'plugin_manager' => [], <-- Configuration for the plugin manager
    ]
]
```

### Usage:
See https://flysystem.thephpleague.com/docs/advanced/mount-manager

```php

```php
class X {
    __construct(FileManager $fileManager) {
        $contents = $manager->read('local://some/file.txt');
        $contents = $manager->read('s3://some/file.txt');
    }
}
```

## DiskManager

This is the manager than handle all your disks (Filesystem).
You can use the diskmanager directly but ideally you would only interact with the FileManager.

### Config

This is the default configuration.
A `local` and a `s3` file system are defined (You will need to change the s3 credentials)

```php
return [
    'filesystem' => [
        'disks' => [
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
                    'disable_asserts' => true,
                    'visibility' => 'private',
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

- `null` -> `League\Flysystem\Adapter\NullAdapter`
- `local` -> `League\Flysystem\Adapter\Local`
- `file`  -> `League\Flysystem\Adapter\Local`
- `memory` -> `League\Flysystem\Memory\MemoryAdapter`
- `s3` `League\Flysystem\AwsS3v2\AwsS3Adapter` (requires `league/flysystem-aws-s3-v3`)
- `dropbox` -> `Spatie\FlysystemDropbox\DropboxAdapter` (requires `spatie/flysystem-dropbox`)
- `ftp` -> `League\Flysystem\Adapter\Ftp` (requires `league/flysystem-sftp`)

### Usage

```php
class X {
    __construct(DiskManager $diskManager) {

        // Use default disk.
        $diskManager->default()->get('path/to/file');

        // Use cloud disk.
        $diskManager->cloud()->get('path/to/file');

        // Use specific disk
        $diskManager->disk('another')->get('path/to/file');
    }
}
```

### Custom Adapters.

The disk manager is a Lamninas Service Manager so you can add your own factories and aliases.
Use the `disk_manager` config

```php
return [
    'filesystem' => [
        'disk_manager' => [
            'factories' => [
                CustomFilesystem::class => Factory::class,
            ],
            'aliases' => [
                'customAdapter' => CustomAdapter::class,
            ] 
        ],
    ],
];
```

Note the disk manager uses an Abstract Factory in order to instantiate filesystems and allow caching

If you want to create Adapter please use aliases that dont link to any factories.

# Caching.

This uses the package `aftdev/cache-manager`

Each disk can define a caching mechanism using a store from the cache manager.
- `store` - Store to use.
- `expire` - seconds until cache expiration. 
- `key` - storage key.

```php
's3' => [
    ...
    'region' => 'your-region',
    'version' => 'latest',
    'cache' => [
        'store' => 'memcached',
        'key' => 's3_file_system',
        'expire' = '60',
    ],
],
```

### In Memory Caching 

If you do not want to use a cache store you could use the In Memory cache simply by setting `cache` to true

```php
's3' => [
    ...
    'region' => 'your-region',
    'version' => 'latest',
    'cache' => true,
],
```

# Plugin Manager

https://flysystem.thephpleague.com/docs/advanced/plugins/

Use the provided plugin manager to add custom function to the FileManager.
The plugin Manager is a Zend Service Manager.

### Usage

`$filemanager->emptyDir('disk://dir');`

### Provided Plugins.

Default plugins provided by flysystem can be used out of the box.

- `emptyDir` 
- `forcedCopy`
- `forcedRename`
- `getWithMetadata`
- `listFiles`
- `listPaths`
- `listWith`

### Custom Plugins.

To add custom functions to the manager define the alias and factory to the 
service manager configuration.

```php
return [
    'filesystem' => [
        'plugin_manager' => [
            'aliases' => [
                'yourCoolNewFunction' => YourPluginClass:class,
            ],
            'factories' => [
                YourPluginClass:class => InvokableFactory::class,
            ]
        ],
    ]
]
```

`$filemanager->yourCoolNewFunction('disk://path/to/file');`

# Notes

Depending on the disk adapter you chose, you will need to require extra packages.

```
league/flysystem suggests installing league/flysystem-azure (Allows you to use Windows Azure Blob storage)
league/flysystem suggests installing league/flysystem-webdav (Allows you to use WebDAV storage)
league/flysystem suggests installing league/flysystem-aws-s3-v3 (Allows you to use S3 storage with AWS SDK v3)
league/flysystem suggests installing spatie/flysystem-dropbox (Allows you to use Dropbox storage)
league/flysystem suggests installing srmklive/flysystem-dropbox-v2 (Allows you to use Dropbox storage for PHP 5 applications)
league/flysystem suggests installing league/flysystem-sftp (Allows you to use SFTP server storage via phpseclib)
league/flysystem suggests installing league/flysystem-ziparchive (Allows you to use ZipArchive adapter)
```
