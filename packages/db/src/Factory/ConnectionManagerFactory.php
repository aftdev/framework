<?php

namespace AftDev\Db\Factory;

use AftDev\Db\ConfigProvider;
use AftDev\Db\ConnectionManager;
use Psr\Container\ContainerInterface;

class ConnectionManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')[ConfigProvider::KEY_DATABASE]['connections'] ?? [];

        return new ConnectionManager($config);
    }
}
