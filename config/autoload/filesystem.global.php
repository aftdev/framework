<?php

use AftDev\Filesystem\ConfigProvider;

return [
    ConfigProvider::CONFIG_KEY => [
        'disks' => [
            'plugins' => [
                'local' => [
                    'location' => realpath(__DIR__.'/../../tests/data/filesystem'),
                ],
                's3' => [
                    'region' => 'us-east-1',
                    'version' => 'latest',
                    'endpoint' => getenv('S3_ENDPOINT') ?: 'http://localstack:4566',
                    'bucket_endpoint' => true,
                    'use_path_style_endpoint' => true,
                ],
            ],
        ],
    ],
];
