<?php

namespace AftDev\Db\Factory;

use AftDev\Db\Connection;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

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
