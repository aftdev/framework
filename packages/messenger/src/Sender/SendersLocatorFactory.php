<?php

namespace AftDev\Messenger\Sender;

use AftDev\Messenger\ConfigProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;

class SendersLocatorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $senders = $container->get('config')[ConfigProvider::KEY_MESSENGER][ConfigProvider::KEY_SENDERS];

        return new SendersLocator($senders, $container);
    }
}
