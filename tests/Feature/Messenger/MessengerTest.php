<?php

namespace AftDev\Test\Feature\Messenger;

use AftDev\Console\Application;
use AftDev\Messenger\Message\QueueableInterface;
use AftDev\Messenger\Message\QueueableTrait;
use AftDev\Messenger\Message\SelfHandlingInterface;
use AftDev\Messenger\Messenger;
use AftDev\Messenger\Queue\QueueManager;
use AftDev\Test\Feature\Messenger\Messages\TestQueuableCommand;
use AftDev\Test\FeatureTestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

/**
 * @internal
 *
 * @covers \AftDev\Messenger\Factory\MessageBusFactory
 * @covers \AftDev\Messenger\Factory\MessengerFactory
 * @covers \AftDev\Messenger\Handler\MessageHandler
 * @covers \AftDev\Messenger\Handler\MessageHandlerFactory
 * @covers \AftDev\Messenger\Message\QueueableTrait
 * @covers \AftDev\Messenger\Messenger
 * @covers \AftDev\Messenger\Middleware\HandleMessageMiddlewareFactory
 * @covers \AftDev\Messenger\Middleware\SendMessageMiddlewareFactory
 * @covers \AftDev\Messenger\Queue\QueueManager
 * @covers \AftDev\Messenger\Queue\QueueManagerFactory
 * @covers \AftDev\Messenger\Sender\QueueSender
 * @covers \AftDev\Messenger\Sender\QueueSenderFactory
 * @covers \AftDev\Messenger\Sender\SendersLocatorFactory
 */
class MessengerTest extends FeatureTestCase
{
    /**
     * @var Messenger
     */
    protected $messenger;

    public function setUp(): void
    {
        parent::setUp();

        // Create another transport for tests.
        $this->overrideConfig('messenger.queues.plugins.memory_two.service', 'memory');

        $this->messenger = $this->container->get(Messenger::class);
    }

    public function testDirectDispatch()
    {
        $message = new class() implements SelfHandlingInterface {
            public $dispatched = false;

            public function handle()
            {
                $this->dispatched = true;
            }
        };

        $messageWithoutHandler = new class() implements SelfHandlingInterface {
            public $dispatched = false;
        };

        $this->messenger->dispatch($message);
        $this->assertTrue($message->dispatched);

        $this->messenger->dispatch($messageWithoutHandler);
        $this->assertFalse($messageWithoutHandler->dispatched);
    }

    public function testStamps()
    {
        $message = new class() implements SelfHandlingInterface {
            public $dispatched = false;

            public function handle()
            {
                $this->dispatched = true;
            }
        };

        $stamps = [
            new DelayStamp(60),
        ];

        $this->messenger->dispatch($message, $stamps);
        $this->assertTrue($message->dispatched);
    }

    /**
     * Test that the commands are sent to the queue.
     */
    public function testQueueDispatch()
    {
        $commandDefaultQueue = new class() implements SelfHandlingInterface, QueueableInterface {
            use QueueableTrait;

            public $dispatched = false;

            public function handle()
            {
                $this->dispatched = true;
            }
        };

        $commandOtherQueue = new class() implements SelfHandlingInterface, QueueableInterface {
            use QueueableTrait;

            public $dispatched = false;

            public $queue = 'memory_two';

            public function handle()
            {
                $this->dispatched = true;
            }
        };

        $commandOnTwoQueues = new class() implements SelfHandlingInterface, QueueableInterface {
            use QueueableTrait;

            public $dispatched = false;

            public function __construct()
            {
                $this->onQueue('memory', 'memory_two');
            }

            public function handle()
            {
                $this->dispatched = true;
            }
        };

        $this->messenger->dispatch($commandDefaultQueue);
        $this->messenger->dispatch($commandOtherQueue);
        $this->messenger->dispatch($commandOnTwoQueues);

        /** @var QueueManager $queueManager */
        $queueManager = $this->container->get(QueueManager::class);

        // Commands should be sent to queue and not dispatched.
        $this->assertFalse($commandDefaultQueue->dispatched);
        $this->assertFalse($commandOtherQueue->dispatched);
        $this->assertFalse($commandOnTwoQueues->dispatched);

        // Check that the messages have been set to the proper queues.
        $defaultQueue = $queueManager->queue();
        $otherQueue = $queueManager->queue('memory_two');

        $defaultQueueMessages = $defaultQueue->get();
        $this->assertCount(2, $defaultQueueMessages);

        $firstEnvelop = current($defaultQueueMessages);
        $secondEnvelop = next($defaultQueueMessages);
        $this->assertSame($commandDefaultQueue, $firstEnvelop->getMessage());
        $this->assertSame($commandOnTwoQueues, $secondEnvelop->getMessage());

        $otherQueueMessages = $otherQueue->get();
        $this->assertCount(2, $otherQueueMessages);

        $firstEnvelop = current($otherQueueMessages);
        $secondEnvelop = next($otherQueueMessages);
        $this->assertSame($commandOtherQueue, $firstEnvelop->getMessage());
        $this->assertSame($commandOnTwoQueues, $secondEnvelop->getMessage());
    }

    /**
     * @covers \AftDev\Messenger\Console\ConsumeCommandFactory
     */
    public function testConsumer()
    {
        /** @var Application $console */
        $console = $this->container->get(Application::class);

        $mockInfo = $this->prophesize(LoggerInterface::class);
        $this->mockService(LoggerInterface::class, $mockInfo);

        // Dispatch one command on the redis queue.
        $command = new TestQueuableCommand();
        $command->onQueue('redis');

        $this->messenger->dispatch($command);

        // Start the consumer for 1 seconds max and 1 limit.
        // The command should already be in the queue so it should not take longer that 1s
        $console->call('messenger:consume', [
            'receivers' => ['redis'],
            '--limit' => '1',
            '--time-limit' => '1',
        ]);

        // Assert that the worker found the message and handled it.
        $mockInfo
            ->info(Argument::containingString('Command {class} was handled'), Argument::any())
            ->shouldHaveBeenCalledOnce()
        ;
    }
}
