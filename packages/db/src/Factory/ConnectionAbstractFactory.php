<?php

namespace AftDev\Db\Factory;

use AftDev\Db\Connection;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;

class ConnectionAbstractFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Connection($options ?? []);
    }

    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return true;
    }
}
