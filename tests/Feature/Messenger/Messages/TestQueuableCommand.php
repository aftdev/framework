<?php

namespace AftDev\Test\Feature\Messenger\Messages;

use AftDev\Messenger\Message\QueueableInterface;
use AftDev\Messenger\Message\QueueableTrait;
use AftDev\Messenger\Message\SelfHandlingInterface;
use Psr\Log\LoggerInterface;

class TestQueuableCommand implements QueueableInterface, SelfHandlingInterface
{
    use QueueableTrait;

    public function handle(LoggerInterface $logger)
    {
        $logger->info('Command {class} was handled', ['class' => __CLASS__]);
    }
}
