<?php

namespace AftDev\Db\Migration;

use AftDev\Db\ConfigProvider;
use AftDev\Db\Connection;
use AftDev\Db\ConnectionManager;
use Phinx\Config\Config;
use Psr\Container\ContainerInterface;

class PhinxConfigFactory
{
    public function __invoke(ContainerInterface $container): Config
    {
        $configArray = $this->getPhinxConfigFromConnectionManager($container);

        return new Config($configArray);
    }

    public function getPhinxConfigFromConnectionManager(ContainerInterface $container): array
    {
        $connectionManager = $container->get(ConnectionManager::class);
        $migrationConfig = $container->get('config')[ConfigProvider::KEY_DATABASE]['migrations'] ?? [];

        $configArray = [
            'paths' => [
                'migrations' => $migrationConfig['paths'] ?? [],
                'seeds' => $migrationConfig['seeds'] ?? [],
            ],
            'environments' => [
                'default_migration_table' => 'migrations',
                'default_database' => 'default',
            ],
        ];

        $default = null;
        $connections = $connectionManager->getAll();
        foreach ($connections as $name => $connection) {
            $configArray['environments'][$name] = $this->connectionToPhinxEnv(
                $connection
            );

            if (true === $connection->getDefault()) {
                $default = $name;
            }
        }

        reset($configArray);
        $configArray['environments']['default_database'] = $default ?? key($configArray['environments']);

        return $configArray;
    }

    /**
     * Transform Connection value to Phinx Env.
     */
    protected function connectionToPhinxEnv(Connection $connection): array
    {
        // mysql, pgsql, sqlite, sqlsrv.
        $phinxAdapterMap = [
            'mysql' => 'mysql',
            'postgresql' => 'pgsql',
            'sql' => 'sqlsrv',
            'sqlsrv' => 'sqlsrv',
            'sqlite' => 'sqlite',
        ];

        $fieldMap = [
            'hostname' => 'host',
            'database' => 'name',
            'username' => 'user',
            'password' => 'pass',
        ];

        $phinxConnection = $connection->toArray();

        $type = $connection->getType() ?? 'mysql';
        $phinxConnection['adapter'] = $phinxAdapterMap[$type] ?? 'mysql';
        unset($phinxConnection['type']);

        foreach ($fieldMap as $key => $map) {
            if (isset($connection->{$key})) {
                $phinxConnection[$map] = $connection->{$key};
                unset($phinxConnection[$key]);
            }
        }

        return $phinxConnection;
    }
}
