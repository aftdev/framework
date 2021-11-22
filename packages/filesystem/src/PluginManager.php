<?php

namespace AftDev\Filesystem;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use League\Flysystem\Plugin\EmptyDir;
use League\Flysystem\Plugin\ForcedCopy;
use League\Flysystem\Plugin\ForcedRename;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Plugin\ListPaths;
use League\Flysystem\Plugin\ListWith;
use League\Flysystem\PluginInterface;

class PluginManager extends ServiceManager
{
    /**
     * {@inheritdoc}
     */
    protected $instanceOf = PluginInterface::class;

    /**
     * {@inheritdoc}
     */
    protected $factories = [
        EmptyDir::class => InvokableFactory::class,
        ForcedCopy::class => InvokableFactory::class,
        ForcedRename::class => InvokableFactory::class,
        GetWithMetadata::class => InvokableFactory::class,
        ListFiles::class => InvokableFactory::class,
        ListPaths::class => InvokableFactory::class,
        ListWith::class => InvokableFactory::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected $aliases = [
        'emptyDir' => EmptyDir::class,
        'forcedCopy' => ForcedCopy::class,
        'forcedRename' => ForcedRename::class,
        'getWithMetadata' => GetWithMetadata::class,
        'listFiles' => ListFiles::class,
        'listPaths' => ListPaths::class,
        'listWith' => ListWith::class,
    ];
}
