<?php

namespace AftDev\DbEloquent\Capsule;

use AftDev\Db\ConnectionManager;
use Illuminate\Container\Container as IlluminateContainer;
use Psr\Container\ContainerInterface;

class CapsuleManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $connectionManager = $container->get(ConnectionManager::class);

        $connections = $connectionManager->getAll();
        $capsuleContainer = new IlluminateContainer();

        $capsuleManager = new CapsuleManager($capsuleContainer);
        $capsuleManager->setConnections($connections);

        return $capsuleManager;
    }
}
