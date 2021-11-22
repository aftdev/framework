<?php

use AftDev\Messenger\ConfigProvider;

return [
    ConfigProvider::KEY_MESSENGER => [
        ConfigProvider::KEY_QUEUES => [
            'plugins' => [
                'redis' => [
                    'dsn' => getenv('REDIS_SERVER'),
                ],
            ],
        ],
    ],
];
