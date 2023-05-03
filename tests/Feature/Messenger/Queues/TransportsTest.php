<?php

namespace AftDev\Test\Feature\Messenger\Queues;

use AftDev\Messenger\Queue\QueueManager;
use AftDev\Test\Feature\Messenger\Messages\TestCommand;
use AftDev\Test\FeatureTestCase;
use Illuminate\Support\Arr;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @internal
 *
 * @covers \AftDev\Messenger\Queue\Factory\SymfonyTransportAbstractFactory
 */
final class TransportsTest extends FeatureTestCase
{
    /**
     * @dataProvider transportProviders()
     */
    public function testTransport(string $transportName): void
    {
        $queueManager = $this->container->get(QueueManager::class);

        /** @var TransportInterface $redisTransport */
        $redisTransport = $queueManager->queue($transportName);

        $envelope = new Envelope(new TestCommand());
        $redisTransport->send($envelope);

        $messages = $redisTransport->get();
        $this->assertCount(1, $messages);

        foreach ($messages as $message) {
            $redisTransport->ack($message);
        }

        $messages = $redisTransport->get();
        $this->assertCount(0, $messages);
    }

    public static function transportProviders()
    {
        return [
            'memory transport' => ['transport' => 'memory'],
            'redis transport' => ['transport' => 'redis'],
        ];
    }

    public function testInvalidTransport(): void
    {
        $config = $this->container->get('config');

        Arr::set($config, 'messenger.queues.plugins.invalid.dsn', 'invalid://sdfdf');

        $queueManager = $this->container->get(QueueManager::class);

        $this->expectException(ServiceNotCreatedException::class);

        $queueManager->queue('invalid');
    }
}
