<?php

namespace AftDevTest\Console;

use AftDev\Console\Application;
use AftDev\Test\TestCase;

/**
 * Class ApplicationTest.
 *
 * @internal
 *
 * @covers \AftDev\Console\Application
 */
class ApplicationTest extends TestCase
{
    /**
     * Test that the call function works properly.
     */
    public function testCall()
    {
        $application = new Application();

        $application->add(new Command());

        $test = $application->call('testCommand', []);

        $this->assertEquals(0, $test);
    }
}
