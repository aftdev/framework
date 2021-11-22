<?php

namespace AftDev\Db;

use Laminas\Stdlib\AbstractOptions;

class Connection extends AbstractOptions implements ConnectionInterface
{
    protected bool $default = false;

    protected bool $debug = false;

    protected string $type = 'mysql';

    protected string $database = '';

    protected string $hostname = '';

    protected int $port = 3306;

    protected string $username = 'root';

    protected string $password = 'root';

    protected string $charset = 'utf8mb4';

    protected array $extra = [];

    protected string $collation = 'utf8mb4_unicode_ci';

    public function getDefault(): bool
    {
        return (bool) $this->default;
    }

    public function setDefault(bool $default)
    {
        $this->default = $default;
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database)
    {
        $this->database = $database;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname)
    {
        $this->hostname = $hostname;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port)
    {
        $this->port = $port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function setCharset(string $charset)
    {
        $this->charset = $charset;
    }

    public function getCollation(): string
    {
        return $this->collation;
    }

    public function setCollation(string $collation)
    {
        $this->collation = $collation;
    }

    /**
     * Extra settings.
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * Array containing extra settings.
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
    }
}
