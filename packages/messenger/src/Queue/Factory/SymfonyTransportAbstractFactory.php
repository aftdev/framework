<?php

namespace AftDev\Messenger\Queue\Factory;

//use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpTransportFactory;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransportFactory;
use Symfony\Component\Messenger\Bridge\Redis\Transport\RedisTransportFactory;
use Symfony\Component\Messenger\Transport\InMemoryTransportFactory;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;

/**
 * Abstract factory to create Symfony transport from dsn option.
 *
 * @see https://symfony.com/doc/current/messenger.html#transport-configuration
 */
class SymfonyTransportAbstractFactory implements AbstractFactoryInterface
{
    public const FACTORY_MAPPING = [
        'in-memory' => InMemoryTransportFactory::class,
        'memory' => InMemoryTransportFactory::class,
        'redis' => RedisTransportFactory::class,
        'amqp' => AmqpTransportFactory::class,
        'amqps' => AmqpTransportFactory::class,
        'doctrine' => DoctrineTransportFactory::class,
    ];

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dsn = $options['dsn'] ?? $requestedName.'://';
        unset($options['dsn']);
        $factoryType = explode('://', $dsn)[0];

        $factoryName = self::FACTORY_MAPPING[$factoryType] ?? null;
        if (null === $factoryName) {
            throw new ServiceNotCreatedException(sprintf(
                'Could not find symfony transport factory for dsn %s in %s',
                $dsn,
                get_class($this)
            ));
        }

        /** @var TransportFactoryInterface $factory */
        $factory = new ($factoryName)();
        $serializer = $this->getSerializer($container);

        return $factory->createTransport($dsn, $options, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        // Always return true - we will use the options or name above to check if we can handle it.
        return true;
    }

    protected function getSerializer(ContainerInterface $container): SerializerInterface
    {
        $serializerClass = $options['serializer'] ?? Serializer::class;

        return $container->has($serializerClass)
            ? $container->get($serializerClass)
            : new $serializerClass();
    }
}
