<?php

namespace AftDev\Messenger\Queue;

use AftDev\ServiceManager\AbstractManager;
use Symfony\Component\Messenger\Transport\TransportInterface;

class QueueManager extends AbstractManager
{
    public $instanceOf = TransportInterface::class;

    protected $default = 'memory';

    /**
     * Get the Transport.
     */
    public function queue(string $name = null): TransportInterface
    {
        return $name ? $this->getPlugin($name) : $this->getDefault();
    }
}
