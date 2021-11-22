<?php

namespace AftDev\Messenger;

use AftDev\Console;
use AftDev\Messenger\Console as MessengerConsole;
use AftDev\Messenger\Handler\MessageHandler;
use AftDev\Messenger\Message\QueueableInterface;
use AftDev\Messenger\Message\SelfHandlingInterface;
use AftDev\Messenger\Queue\Factory\SymfonyTransportAbstractFactory;
use AftDev\Messenger\Sender\QueueSender;
use AftDev\Messenger\Serializer\Normalizer;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Symfony\Component\Messenger as SymfonyMessenger;
use Symfony\Component\Messenger\Transport as SymfonyMessengerTransport;
use Symfony\Component\Serializer\Normalizer as SymfonyNormalizer;

class ConfigProvider
{
    public const KEY_MESSENGER = 'messenger';

    public const KEY_MIDDLEWARES = 'middlewares';

    public const KEY_HANDLERS = 'handlers';

    public const KEY_SENDERS = 'senders';

    public const KEY_QUEUES = 'queues';

    public const KEY_NORMALIZERS = 'normalizers';

    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            self::KEY_MESSENGER => [
                self::KEY_MIDDLEWARES => $this->getMiddlewareList(),
                self::KEY_HANDLERS => $this->getHandlerList(),
                self::KEY_SENDERS => $this->getSenderList(),
                self::KEY_QUEUES => $this->getQueuesConfig(),
                self::KEY_NORMALIZERS => $this->getNormalizers(),
            ],
            Console\ConfigProvider::KEY_CONSOLE => $this->getConsoleConfig(),
        ];
    }

    public function getDependencies()
    {
        return [
            'factories' => [
                Messenger::class => Factory\MessengerFactory::class,
                SymfonyMessenger\MessageBusInterface::class => Factory\MessageBusFactory::class,

                // Symfony Middlewares.
                SymfonyMessenger\Middleware\HandleMessageMiddleware::class => Middleware\HandleMessageMiddlewareFactory::class,
                SymfonyMessenger\Middleware\SendMessageMiddleware::class => Middleware\SendMessageMiddlewareFactory::class,

                // Message Handlers.
                Handler\MessageHandler::class => Handler\MessageHandlerFactory::class,

                // Senders.
                SymfonyMessengerTransport\Sender\SendersLocator::class => Sender\SendersLocatorFactory::class,
                Sender\QueueSender::class => Sender\QueueSenderFactory::class,

                // Serializer.
                Serializer\QueueSerializer::class => Serializer\QueueSerializerFactory::class,

                // - Normalizers
                SymfonyNormalizer\PropertyNormalizer::class => Serializer\Normalizer\PropertyNormalizerFactory::class,
                SymfonyNormalizer\ObjectNormalizer::class => Serializer\Normalizer\ObjectNormalizerFactory::class,
                Serializer\Normalizer\CarbonDenormalizer::class => InvokableFactory::class,

                // Queues.
                Queue\QueueManager::class => Queue\QueueManagerFactory::class,
                SymfonyMessengerTransport\Serialization\Serializer::class => Serializer\MessageSerializerFactory::class,
            ],
        ];
    }

    /**
     * List of middlewares used by the command bus.
     */
    public function getMiddlewareList(): array
    {
        return [
            'send' => [SymfonyMessenger\Middleware\SendMessageMiddleware::class, 1000],
            'handle' => [SymfonyMessenger\Middleware\HandleMessageMiddleware::class, 0],
        ];
    }

    /**
     * List of command message handlers.
     */
    public function getHandlerList(): array
    {
        return [
            SelfHandlingInterface::class => [
                'default' => MessageHandler::class,
            ],
        ];
    }

    /**
     * Transport Manager configuration.
     */
    public function getQueuesConfig(): array
    {
        return [
            'default' => 'memory',
            'plugins' => [
                'memory' => [
                    'dsn' => 'in-memory://',
                ],
                'redis' => [
                    'dsn' => 'redis://redis:6379',
                    'delete_after_ack' => true,
                    'lazy' => true,
                ],
            ],
            'abstract_factories' => [
                new SymfonyTransportAbstractFactory(),
            ],
        ];
    }

    /**
     * Senders Lists.
     */
    public function getSenderList(): array
    {
        return [
            QueueableInterface::class => [
                'default' => QueueSender::class,
            ],
        ];
    }

    /**
     * Define console configuration.
     *
     * @return array
     */
    public function getConsoleConfig()
    {
        return [
            Console\ConfigProvider::KEY_COMMANDS => [
                SymfonyMessenger\Command\ConsumeMessagesCommand::getDefaultName() => SymfonyMessenger\Command\ConsumeMessagesCommand::class,
            ],
            Console\ConfigProvider::KEY_COMMAND_MANAGER => [
                'factories' => [
                    SymfonyMessenger\Command\ConsumeMessagesCommand::class => MessengerConsole\ConsumeCommandFactory::class,
                ],
            ],
        ];
    }

    public function getNormalizers()
    {
        return [
            Normalizer\CarbonDenormalizer::class => Normalizer\CarbonDenormalizer::class,
            SymfonyNormalizer\DateTimeNormalizer::class => SymfonyNormalizer\DateTimeNormalizer::class,
            // Use Property Normalizer before Object Normalizer as the later is slower.
            SymfonyNormalizer\PropertyNormalizer::class => SymfonyNormalizer\PropertyNormalizer::class,
            SymfonyNormalizer\ObjectNormalizer::class => SymfonyNormalizer\ObjectNormalizer::class,
        ];
    }
}
