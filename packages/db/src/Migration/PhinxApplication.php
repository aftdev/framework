<?php

namespace AftDev\Db\Migration;

use AftDev\Console\CallableTrait;
use Phinx\Config\Config;
use Phinx\Console\Command\AbstractCommand;
use Phinx\Console\PhinxApplication as ParentApplication;

class PhinxApplication extends ParentApplication
{
    use CallableTrait;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;
        $this->find('init')->setHidden(true);
        $this->find('test')->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
    public function find($name)
    {
        $command = parent::find($name);

        if ($command instanceof AbstractCommand) {
            $command->setConfig($this->config);
        }

        return $command;
    }
}
