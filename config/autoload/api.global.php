<?php

use AftDev\Api\ConfigProvider;

return [
    ConfigProvider::CONFIG_KEY => [
        'spec' => realpath('tests/data/openapi/petstore.yaml'),
    ],
];
