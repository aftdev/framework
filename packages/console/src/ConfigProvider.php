<?php

namespace AftDev\Console;

use AftDev\ServiceManager\Factory\ResolverAbstractFactory;

class ConfigProvider
{
    /**
     * Key for the console commands list.
     */
    public const KEY_CONSOLE = 'console';

    public const KEY_COMMANDS = 'commands';

    /**
     * Key for the command manager config.
     */
    public const KEY_COMMAND_MANAGER = 'command_manager';

    public function __invoke()
    {
        $config['dependencies'] = $this->getDependencyConfig();
        $config[self::KEY_CONSOLE][self::KEY_COMMANDS] = $this->getCommands();
        $config[self::KEY_CONSOLE][self::KEY_COMMAND_MANAGER] = $this->getManagerConfig();

        return $config;
    }

    public function getDependencyConfig()
    {
        return [
            'factories' => [
                Application::class => Factory\ApplicationFactory::class,
                CommandManager::class => Factory\CommandManagerFactory::class,
            ],
        ];
    }

    /**
     * Get list of commands.
     */
    public function getCommands(): array
    {
        return [];
    }

    /**
     * Get Command Manager Config.
     */
    public function getManagerConfig(): array
    {
        return [
            'abstract_factories' => [
                'default' => ResolverAbstractFactory::class,
            ],
        ];
    }
}
