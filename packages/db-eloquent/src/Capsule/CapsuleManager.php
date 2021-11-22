<?php

namespace AftDev\DbEloquent\Capsule;

use AftDev\Db\Connection as DbConnection;
use Illuminate\Database\Capsule\Manager;

/**
 * Class CapsuleManager.
 */
class CapsuleManager extends Manager
{
    /**
     * Set Connection Settings.
     *
     * @param DbConnection[] $connections
     */
    public function setConnections(array $connections)
    {
        $defaultConnection = 'default';
        foreach ($connections as $name => $connection) {
            $eloquentConnection = $this->getEloquentConnectionArray($connection);
            $this->addConnection($eloquentConnection, $name);
            if ($connection->getDefault()) {
                $defaultConnection = $name;
            }
        }

        // Set Default.
        $this->getDatabaseManager()->setDefaultConnection($defaultConnection);
    }

    /**
     * Convert "Db" connection array to an illuminate format.
     */
    protected function getEloquentConnectionArray(DbConnection $connection): array
    {
        $eloquent = [
            'driver' => $connection->getType(),
            'host' => $connection->getHostname(),
            'database' => $connection->getDatabase(),
            'username' => $connection->getUsername(),
            'password' => $connection->getPassword(),
            'charset' => $connection->getCharset(),
            'collation' => $connection->getCollation(),
            'port' => $connection->getPort(),
        ];

        $extra = $connection->getExtra();
        $eloquent += $extra;

        return $eloquent;
    }
}
