<?php

use AftDev\Api\ConfigProvider;
use Laminas\Stdlib\ArrayUtils\MergeRemoveKey;

return [
    ConfigProvider::CONFIG_KEY => [
        'spec' => realpath('tests/data/openapi/petstore.yaml'),
        'servers' => [
            'default' => MergeRemoveKey::class,
            'development' => [
                'url' => '',
                'description' => 'Development server',
            ],
        ],
    ],
];
