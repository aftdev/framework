<?php

namespace AftDev\Db;

use AftDev\Db\Factory\ConnectionAbstractFactory;
use AftDev\Db\Factory\ConnectionManagerFactory;
use AftDev\Db\Migration\PhinxApplication;
use Phinx\Config\Config;

class ConfigProvider
{
    /**
     * Key for the database config section.
     */
    public const KEY_DATABASE = 'database';

    public function __invoke()
    {
        $config['dependencies'] = $this->getDependencyConfig();
        $config[self::KEY_DATABASE] = [
            'connections' => $this->getConnectionConfig(),
            'connection_manager' => $this->getConnectionManagerConfig(),
            'migrations' => $this->getMigrationConfig(),
        ];

        return $config;
    }

    public function getDependencyConfig()
    {
        return [
            'factories' => [
                ConnectionManager::class => ConnectionManagerFactory::class,
                Config::class => Migration\PhinxConfigFactory::class,
                PhinxApplication::class => Migration\PhinxApplicationFactory::class,
            ],
        ];
    }

    public function getConnectionConfig()
    {
        return [
            'mysql' => [
                'hostname' => 'db_host',
                'database' => 'test',
                'username' => 'root',
                'password' => 'root',
                'port' => 3306,
                'default' => true,
            ],
        ];
    }

    public function getMigrationConfig()
    {
        return [
            'paths' => [
                './db/migrations',
            ],
            'seeds' => [
                './db/seeding',
            ],
        ];
    }

    public function getConnectionManagerConfig()
    {
        return [
            'abstract_factories' => [
                'default' => ConnectionAbstractFactory::class,
            ],
        ];
    }
}
