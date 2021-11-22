<?php

namespace AftDev\Test\Feature\Log;

use AftDev\Log\LoggerManager;
use AftDev\Test\FeatureTestCase;
use Psr\Log\LoggerInterface;

/**
 * @internal
 *
 * @covers \AftDev\Log\LoggerManager
 */
class LogTest extends FeatureTestCase
{
    /**
     * @var LoggerManager
     */
    protected $loggerManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->loggerManager = $this->container->get(LoggerManager::class);
    }

    /**
     * Test Default log.
     *
     * @covers \AftDev\Log\Factory\DefaultLoggerFactory
     */
    public function testDefaultLog()
    {
        $defaultLog = $this->container->get(LoggerInterface::class);

        $this->assertInstanceOf(LoggerInterface::class, $defaultLog);
    }
}
