<?php

namespace AftDev\Test\Feature\Messenger\Queues;

use AftDev\Messenger\Queue\QueueManager;
use AftDev\Test\Feature\Messenger\Messages\TestCommand;
use AftDev\Test\FeatureTest;
use Symfony\Component\Messenger\Envelope;

/**
 * Class RedisTest.
 *
 * @internal
 * @covers \AftDev\Messenger\Queue\Factory\RedisFactory
 */
class RedisTest extends FeatureTest
{
    /**
     * @var QueueManager
     */
    protected $queueManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->queueManager = $this->container->get(QueueManager::class);
    }

    public function testRedisTransport()
    {
        /** @var \Symfony\Component\Messenger\Transport\RedisExt\RedisTransport $redisTransport */
        $redisTransport = $this->queueManager->queue('redis');

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
}
