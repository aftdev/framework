<?php

namespace AftDev\Messenger\Message;

trait QueueableTrait
{
    /**
     * @var array
     */
    protected $queues;

    public function getQueues(): ?array
    {
        return null !== $this->queues
            ? $this->queues
            : (array) ($this->queue ?? null);
    }

    public function onQueue(...$queues): QueueableInterface
    {
        $queues = is_array($queues[0]) ? $queues[0] : $queues;
        $this->queues = (array) $queues;

        return $this;
    }
}
