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
        ],
    ],
];
