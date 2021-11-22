<?php

return [
    'database' => [
        'default' => getenv('DB_CONNECTION') ?: 'test',
        'connections' => [
            'test' => [
                'type' => 'mysql',
                'default' => true,
                'database' => getenv('TEST_DB_NAME'),
                'hostname' => getenv('TEST_DB_HOSTNAME'),
                'username' => getenv('TEST_DB_USERNAME'),
                'password' => getenv('TEST_DB_PASSWORD'),
                'port' => intval(getenv('TEST_DB_PORT')),
            ],
        ],
        'migrations' => [
            'paths' => [
                'tests/migrations',
                'packages/*/migrations',
            ],
        ],
    ],
];
