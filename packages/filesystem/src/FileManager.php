<?php

namespace AftDev\Filesystem;

use League\Flysystem\MountManager;
use League\Flysystem\Plugin\PluginNotFoundException;

class FileManager extends MountManager
{
    /**
     * @var DiskManager
     */
    protected $diskManager;

    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * Filesystem constructor.
     *
     * @param array $filesystems
     */
    public function __construct($filesystems, DiskManager $diskManager, PluginManager $pluginManager)
    {
        parent::__construct($filesystems);

        $this->setDiskManager($diskManager);
        $this->setPluginManager($pluginManager);
    }

    public function getDiskManager(): DiskManager
    {
        return $this->diskManager;
    }

    public function setDiskManager(DiskManager $diskManager)
    {
        $this->diskManager = $diskManager;
    }

    public function getPluginManager(): PluginManager
    {
        return $this->pluginManager;
    }

    public function setPluginManager(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * {@inheritdoc}
     *
     * Will automatically mount the correct filesystem corresponding to the disk
     */
    public function getFilesystem($prefix)
    {
        if (!isset($this->filesystems[$prefix])) {
            // Do we have an filesystem with that prefix ?
            $diskManager = $this->getDiskManager();
            $disk = $diskManager->hasPlugin($prefix)
                ? $diskManager->disk($prefix)
                : false;
            if ($disk) {
                $this->mountFilesystem($prefix, $disk);
            }
        }

        return parent::getFilesystem($prefix);
    }

    /**
     * {@inheritdoc}
     *
     * Will automatically load the correct plugin from the method name.
     */
    protected function findPlugin($method)
    {
        if (!isset($this->plugins[$method])) {
            // Do we have a plugin for this method in our adapter?
            $plugin = $this->getPluginManager()->has($method) ? $this->getPluginManager()->get($method) : false;
            if ($plugin) {
                $this->plugins[$method] = $plugin;
            } else {
                throw new PluginNotFoundException('[[Plugin not found for method: '.$method);
            }
        }

        return $this->plugins[$method];
    }
}
