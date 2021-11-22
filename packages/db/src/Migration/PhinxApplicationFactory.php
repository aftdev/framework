<?php

namespace AftDev\Db\Migration;

use Phinx\Config\Config;
use Psr\Container\ContainerInterface;

class PhinxApplicationFactory
{
    public function __invoke(ContainerInterface $container): PhinxApplication
    {
        $config = $container->get(Config::class);

        return new PhinxApplication($config);
    }
}
