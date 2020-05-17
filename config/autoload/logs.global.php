<?php

use AftDev\Log\ConfigProvider;

return [
    ConfigProvider::CONFIG_KEY => [
        'plugins' => [
            'daily' => [
                'filename' => getenv('LOG_FILE') ?: 'tests/data/log/daily.log',
            ],
            'filesystem' => [
                'options' => [
                    'filename' => getenv('LOG_FILE') ?: 'tests/data/log/application.log',
                ],
            ],
        ],
    ],
];
