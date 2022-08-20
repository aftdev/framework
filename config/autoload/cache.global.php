<?php

return [
    'cache' => [
        'default' => 'array',
        'plugins' => [
            'array' => [
            ],
            'memcached' => [
                'servers' => getenv('MEMCACHED_SERVER'),
            ],
            'redis' => [
                'servers' => getenv('REDIS_SERVER') ?: 'redis://localhost:6379',
            ],
            'php' => [
                'directory' => realpath(__DIR__.'/../../tests/data/cache'),
            ],
            'filesystem' => [
                'options' => [
                    'directory' => realpath(__DIR__.'/../../tests/data/cache'),
                ],
            ],
        ],
    ],
];
