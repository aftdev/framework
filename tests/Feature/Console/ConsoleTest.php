<?php

namespace AftDev\Test\Feature\Console;

use AftDev\Console\Application;
use AftDev\Test\FeatureTestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;

/**
 * @internal
 *
 * @covers \AftDev\Console\Application
 */
class ConsoleTest extends FeatureTestCase
{
    /**
     * @var Application
     */
    protected $console;

    public function setUp(): void
    {
        parent::setUp();

        $this->console = $this->container->get(Application::class);
    }

    public function testCallable()
    {
        $arguments = [
            'argumentA' => 'test argA',
            '--optionA' => 'test option',
        ];

        $result = $this->console->call('test:command', $arguments);
        $output = $this->console->output();

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('Test Command Output', $output);
        $this->assertStringContainsString('ArgA: test argA', $output);
        $this->assertStringContainsString('OptionA: test option', $output);

        $result = $this->console->call('test:command');
        $output = $this->console->output();

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('Test Command Output', $output);
        $this->assertStringContainsString('ArgA: NULL', $output);
        $this->assertStringContainsString('OptionA: NULL', $output);
    }

    public function testInvalidOption()
    {
        $this->expectException(InvalidOptionException::class);
        $arguments = [
            '--invalidOptions' => 'a',
        ];

        $this->console->call('test:command', $arguments);
    }

    public function testInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $arguments = [
            'invalidArgument' => true,
        ];

        $this->console->call('test:command', $arguments);
    }

    public function testUnknownCommand()
    {
        $this->expectException(CommandNotFoundException::class);
        $this->console->call('unknown-command');
    }
}
