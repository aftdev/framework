<?php

namespace AftDev\Console;

use Laminas\ServiceManager\AbstractPluginManager;
use Symfony\Component\Console\Command\Command;

class CommandManager extends AbstractPluginManager
{
    public $instanceOf = Command::class;
}
