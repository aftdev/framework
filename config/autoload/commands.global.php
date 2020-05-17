<?php

use AftDev\Console\ConfigProvider;
use AftDev\Test\Feature\Console\TestCommand;

return [
    ConfigProvider::KEY_CONSOLE => [
        ConfigProvider::KEY_COMMANDS => [
            TestCommand::getDefaultName() => TestCommand::class,
        ],
    ],
];
