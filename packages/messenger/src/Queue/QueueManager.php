<?php

namespace AftDev\Messenger\Queue;

use AftDev\ServiceManager\AbstractManager;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Symfony\Component\Messenger\Transport;

class QueueManager extends AbstractManager
{
    public $instanceOf = Transport\TransportInterface::class;

    protected $aliases = [
        'memory' => Transport\InMemoryTransport::class,
        'redis' => Transport\RedisExt\RedisTransport::class,
    ];

    protected $factories = [
        Transport\InMemoryTransport::class => InvokableFactory::class,
        Transport\RedisExt\RedisTransport::class => Factory\RedisFactory::class,
    ];

    protected $default = 'memory';

    /**
     * Get the Transport.
     */
    public function queue(string $name = null): Transport\TransportInterface
    {
        return $name ? $this->getPlugin($name) : $this->getDefault();
    }
}
