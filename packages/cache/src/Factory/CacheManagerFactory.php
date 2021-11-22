<?php

namespace AftDev\Cache\Factory;

use AftDev\Cache\CacheManager;
use AftDev\ServiceManager\Factory\AbstractManagerFactory;

class CacheManagerFactory extends AbstractManagerFactory
{
    protected $managerClass = CacheManager::class;

    protected $configKey = 'cache';
}
