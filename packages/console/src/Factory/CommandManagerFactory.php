<?php

namespace AftDev\Console\Factory;

use AftDev\Console\CommandManager;
use AftDev\Console\ConfigProvider;
use Psr\Container\ContainerInterface;

class CommandManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')[ConfigProvider::KEY_CONSOLE][ConfigProvider::KEY_COMMAND_MANAGER] ?? [];

        return new CommandManager($container, $config);
    }
}
