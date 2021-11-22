<?php

namespace AftDev\Messenger\Serializer;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Serialization\Serializer as MessageSerializer;

class MessageSerializerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $serializer = $container->get(QueueSerializer::class);

        return new MessageSerializer($serializer, 'json');
    }
}
