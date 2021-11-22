<?php

namespace AftDev\Db;

class ConnectionManager
{
    /**
     * @var Connection[]
     */
    protected $connections = [];

    /**
     * The Default connection.
     *
     * @var Connection
     */
    protected $default;

    /**
     * ConnectionManager constructor.
     *
     * @param $connections
     */
    public function __construct(array $connections)
    {
        foreach ($connections as $name => $config) {
            $this->addConnection($name, $config);
        }
    }

    /**
     * Add a connection.
     *
     * @param array|Connection $connection
     */
    public function addConnection(string $name, $connection)
    {
        if (!$connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        if ($connection->getDefault()) {
            $this->default = $connection;
        }

        $this->connections[$name] = $connection;
    }

    /**
     * Get the connection.
     *
     * @throws \InvalidArgumentException - If connection is not found.
     */
    public function connection(string $connectionName = null): Connection
    {
        if (null === $connectionName) {
            return $this->getDefault();
        }

        $connection = $this->connections[$connectionName] ?? null;
        if (!$connection) {
            throw new \InvalidArgumentException('Invalid connection');
        }

        return $this->connections[$connectionName] ?? null;
    }

    /**
     * Get default connection.
     *
     * If no default has been set, the first registered connection will be returned.
     */
    public function getDefault(): Connection
    {
        return $this->default ?? current($this->connections);
    }

    /**
     * Get all the connections.
     *
     * @return Connection[]
     */
    public function getAll(): array
    {
        return $this->connections;
    }
}
