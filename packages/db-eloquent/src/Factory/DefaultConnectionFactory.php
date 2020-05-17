<?php

namespace AftDev\DbEloquent\Factory;

use AftDev\DbEloquent\Capsule\CapsuleManager;
use Psr\Container\ContainerInterface;

class DefaultConnectionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var CapsuleManager $capsuleManager */
        $capsuleManager = $container->get(CapsuleManager::class);

        return $capsuleManager->getConnection();
    }
}
