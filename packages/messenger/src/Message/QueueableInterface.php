<?php

namespace AftDev\Messenger\Message;

interface QueueableInterface
{
    /**
     * Return the queues this message should be sent to.
     */
    public function getQueues(): ?array;

    /**
     * Set the queues that this message will be sent to.
     */
    public function onQueue(...$queues): QueueableInterface;
}
