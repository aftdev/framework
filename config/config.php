<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$processors = [];

$aggregator = new ConfigAggregator(
    [
        \AftDev\ServiceManager\ConfigProvider::class,
        \AftDev\Console\ConfigProvider::class,
        \AftDev\Cache\ConfigProvider::class,
        \AftDev\Log\ConfigProvider::class,
        \AftDev\Filesystem\ConfigProvider::class,
        \AftDev\Db\ConfigProvider::class,
        \AftDev\DbEloquent\ConfigProvider::class,
        \AftDev\Messenger\ConfigProvider::class,

        new PhpFileProvider(realpath(__DIR__).'/autoload/{{,*.}global,{,*.}local}.php'),
    ],
    null,
    $processors
);

return $aggregator->getMergedConfig();
