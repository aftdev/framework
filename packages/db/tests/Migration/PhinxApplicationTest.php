<?php

namespace AftDevTest\Db\Migration;

use AftDev\Db\Migration\PhinxApplication;
use AftDev\Test\TestCase;
use Phinx\Config\Config;

/**
 * @internal
 * @covers \AftDev\Db\Migration\PhinxApplication
 */
class PhinxApplicationTest extends TestCase
{
    public function testApplicationConfig()
    {
        $config = new Config([
            'paths' => [
                'migrations' => [
                    'a',
                    'b',
                ],
            ],
        ]);

        $application = new PhinxApplication($config);

        $command = $application->find('test');
        $this->assertSame($config, $command->getConfig());
    }
}
