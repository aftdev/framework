<?php

namespace AftDev\Console\Factory;

use AftDev\Console\Application;
use AftDev\Console\CommandManager;
use AftDev\Console\ConfigProvider;
use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class ApplicationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $version = InstalledVersions::getPrettyVersion('aftdev/console-manager') ?? '[dev]';

        $application = new Application('Application console', $version);

        $commandManager = $container->get(CommandManager::class);
        $commands = $container->get('config')[ConfigProvider::KEY_CONSOLE][ConfigProvider::KEY_COMMANDS] ?? [];

        $commandLoader = new ContainerCommandLoader($commandManager, $commands);
        $application->setCommandLoader($commandLoader);

        return $application;
    }
}
