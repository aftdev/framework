<?php

namespace AftDev\Messenger\Queue\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\RedisExt\RedisTransportFactory;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;

class RedisFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = [])
    {
        $serializerClass = $options['serializer'] ?? Serializer::class;

        $serializer = $container->has($serializerClass)
            ? $container->get($serializerClass)
            : new $serializerClass();

        $factory = new RedisTransportFactory();

        return $factory->createTransport($options['dsn'] ?: 'redis://', $options, $serializer);
    }
}
