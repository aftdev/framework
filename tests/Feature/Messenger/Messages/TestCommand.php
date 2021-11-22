<?php

namespace AftDev\Test\Feature\Messenger\Messages;

use AftDev\Messenger\Message\SelfHandlingInterface;
use Psr\Log\LoggerInterface;

class TestCommand implements SelfHandlingInterface
{
    public function handle(LoggerInterface $logger)
    {
        $logger->info('Command {class} was handled', ['class' => __CLASS__]);
    }
}
