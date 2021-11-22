<?php

namespace AftDev\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;

class Messenger
{
    /**
     * @var MessageBusInterface
     */
    protected $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * Dispatch a message.
     */
    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $envelope = new Envelope($message);

        if ($stamps) {
            $envelope->with(...$stamps);
        }

        // Always add the bus to use.
        $envelope->with(new BusNameStamp(MessageBusInterface::class));

        return $this->bus->dispatch($envelope, $stamps);
    }
}
