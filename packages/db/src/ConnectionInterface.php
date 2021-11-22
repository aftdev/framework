<?php

namespace AftDev\Db;

interface ConnectionInterface
{
    public function getDefault(): bool;

    public function getDebug(): bool;

    public function setDebug(bool $debug);

    public function getType(): string;

    public function setType(string $type);

    public function getDatabase(): string;

    public function setDatabase(string $database);

    public function getHostname(): string;

    public function setHostname(string $hostname);

    public function getPort(): int;

    public function setPort(int $port);

    public function getUsername(): string;

    public function setUsername(string $username);

    public function getPassword(): string;

    public function setPassword(string $password);
}
