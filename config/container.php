<?php

declare(strict_types=1);

use Laminas\ServiceManager\ServiceManager;

// Load configuration.
$config = require __DIR__.'/config.php';

$dependencies = $config['dependencies'];
$dependencies['services']['config'] = new ArrayObject($config);

// Build container.
return new ServiceManager($dependencies);
