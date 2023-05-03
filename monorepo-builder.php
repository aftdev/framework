<?php

use Symplify\MonorepoBuilder\Config\MBConfig;
// use Symplify\MonorepoBuilder\Release\ReleaseWorker as ReleaseWorker;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([
        __DIR__ . '/packages',
    ]);
};
