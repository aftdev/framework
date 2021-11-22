<?php

namespace AftDevTest\Db\Migration;

use AftDev\Db\Migration\PhinxApplication;
use AftDev\Db\Migration\PhinxApplicationFactory;
use AftDev\Test\TestCase;
use Phinx\Config\Config;
use Phinx\Console\Command\Test as TestCommand;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Db\Migration\PhinxApplicationFactory
 */
class PhinxApplicationFactoryTest extends TestCase
{
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $config = new Config([]);
        $container->get(Config::class)->willReturn($config);

        $factory = new PhinxApplicationFactory();

        $application = $factory($container->reveal());
        $application->add(new TestCommand());

        $this->assertInstanceOf(PhinxApplication::class, $application);
    }
}
