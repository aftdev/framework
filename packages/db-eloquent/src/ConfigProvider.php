<?php

namespace AftDev\DbEloquent;

use AftDev\Messenger\ConfigProvider as MessengerConfig;
use Illuminate\Database\ConnectionInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        $config['dependencies'] = $this->getDependencyConfig();

        // Inject Messenger config onl
        if (class_exists(MessengerConfig::class)) {
            $config[MessengerConfig::KEY_MESSENGER] = $this->getMessengerConfig();
        }

        return $config;
    }

    /**
     * Services provided by this package.
     */
    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                Capsule\CapsuleManager::class => Capsule\CapsuleManagerFactory::class,
                ConnectionInterface::class => Factory\DefaultConnectionFactory::class,
            ],
        ];
    }

    public function getMessengerConfig(): array
    {
        return [
            MessengerConfig::KEY_NORMALIZERS => [
                Serializer\ModelNormalizer::class => Serializer\ModelNormalizer::class,
            ],
        ];
    }
}
