# Cache Manager.

Cache manager using symfony cache. https://symfony.com/doc/current/components/cache.html

## Usage

```bash
composer require aftdev/cache-manager
```

```php
<?php
class X {
    public function __construct(CacheManager $cacheManager) {
        $cacheManager->getDefault()->get('xxxx', function(ItemInterface $item) {
            $item->expiresAfter(3600);
            
            // ... do some HTTP request or heavy computations
            $computedValue = 'foobar';
        
            return $computedValue;
        });

        // Specify store to use
        $cacheManager->store('phpfiles')->get('xxxx', function(ItemInterface $item) {
            $item->expiresAfter(3600);
            
            // ... do some HTTP request or heavy computations
            $computedValue = 'foobar';
        
            return $computedValue;
        });
    }
}

```

### Default Cache

By default the `Symfony\Contracts\Cache\CacheInterface` and `Psr\Cache\CacheItemPoolInterface` (PSR-6) are linked to the default cache adapter from the configuration

You can easily use it in your classes like so.

```php
<?php

use Symfony\Contracts\Cache\CacheInterface;

class X {
    public function __construct(CacheInterface $defaultCache) 
{
        $defaultCache->get('xxxx', function(ItemInterface $item) {
            $item->expiresAfter(3600);
            
            // ... do some HTTP request or heavy computations
            $computedValue = 'foobar';
        
            return $computedValue;
        });
    }
}
```

```php
<?php
class X {
    public function __construct(\Psr\Cache\CacheItemPoolInterface $defaultCache) 
    {
        $productsCount = $defaultCache->getItem('stats.products_count');
        
        // assign a value to the item and save it
        $productsCount->set(4711);
        $defaultCache->save($productsCount);
    }
}
```


## Config

All adapters provided by symfony are usable by the cache manager.

```php
'plugin' => \Path\To\Symfony\Adapter::class,
```

For easy usage, aliases have been created:

```php
'file' => FilesystemAdapter::class,
'php' => PhpFilesAdapter::class,
'redis' => RedisAdapter::class,
'array' => PhpArrayAdapter::class,
'memcached' => MemcachedAdapter::class,
```

Default configuration provided by the cache manager: 

```php
return [
    'cache' => [
        'default' => 'filesystem',
        'default_options' => [
            'namespace' => 'application',
            'defaultLifetime' => 3600,
        ],
        'plugins' => [
            'filesystem' => [
                'service' => 'file',
                'options' => [
                    'directory' => 'data/cache',
                ],
            ],
            'php' => [
                'service' => 'php',
                'options' => [
                    'directory' => 'data/cache',
                ],
            ],
        ],
    ],
];
```

### Chain 

To cache values using more that one adapter you can use the `chain` adapter.

```php
return [
    'cache' => [
         'plugins' => [
            'chain' => [
                'plugin' => 'chain',
                'options' => [
                    'adapter' => ['adapter_1', 'adapter_2'],
                ],
            ]
         ],
         'adapter_1' => [],
         'adapter_2' => [],
    ],
];
```

To create chains on the fly use the `chain` function 

```php
$cacheManager->chain('adapter_1', 'adapter_2')->get('xxxx', function(ItemInterface $item) {
    $item->expiresAfter(3600);
    
    // ... do some HTTP request or heavy computations
    $computedValue = 'foobar';

    return $computedValue;
});
```
