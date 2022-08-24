# Cache Manager (PSR-6)

Cache manager using symfony cache.
https://symfony.com/doc/current/components/cache.html returning PSR-6 cache
interfaces.

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

By default the `Symfony\Contracts\Cache\CacheInterface` and
`Psr\Cache\CacheItemPoolInterface`
([PSR-6](https://www.php-fig.org/psr/psr-6/#cacheitempoolinterface)) are linked
to the default cache adapter from the configuration

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

## PSR-16

Most of the time psr-6 cache interfaces will work great for you. Nonetheless,
this package provides a way to use
[PSR-16](https://www.php-fig.org/psr/psr-16/#21-cacheinterface) interfaces
instead

```php
<?php

use Psr\SimpleCache\CacheInterface as Psr16Interface;
use AftDev\Cache\CacheManager;

// From the container to retrieve the default cache store.
$psr16DefaultStore = $container->get(Psr16Interface::class);


// Or by using the helper function from the CacheManager
$store = $cacheManager->store('XXX');
$psr16Store = CacheManager::psr16($store);
// or
$psr16Store = $cacheManager->psr16($store);
```
