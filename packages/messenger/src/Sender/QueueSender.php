<?php

namespace AftDev\Messenger\Sender;

use AftDev\Messenger\Message\QueueableInterface;
use AftDev\Messenger\Queue\QueueManager;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;

class QueueSender implements SenderInterface
{
    /**
     * @var QueueManager
     */
    protected $queueManager;

    public function __construct(QueueManager $transportManager)
    {
        $this->queueManager = $transportManager;
    }

    /**
     * Send the message to the given queues.
     */
    public function send(Envelope $envelope): Envelope
    {
        /** @var QueueableInterface $message */
        $message = $envelope->getMessage();
        $queues = $message->getQueues() ?: [null];

        foreach (array_unique($queues) as $queue) {
            $queueTransport = $this->queueManager->queue($queue);
            $queueTransport->send($envelope);
        }

        return $envelope;
    }
}
