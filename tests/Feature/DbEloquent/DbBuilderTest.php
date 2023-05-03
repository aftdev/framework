<?php

namespace AftDev\Test\Feature\DbEloquent;

use AftDev\DbEloquent\Capsule\CapsuleManager;
use AftDev\Test\FeatureTestCase;

/**
 * @internal
 *
 * @covers \AftDev\DbEloquent\Capsule\CapsuleManager
 */
class DbBuilderTest extends FeatureTestCase
{
    protected $useDb = true;

    public function testDbBuilder()
    {
        $connectionManager = $this->container->get(CapsuleManager::class);

        /** @var \Illuminate\Database\MySqlConnection $connection */
        $connection = $connectionManager->getConnection();

        $test = $connection->table('test');

        $test->insert([
            'column1' => 'a',
        ]);

        $first = $test->select('*')->first('column1');

        $this->assertEquals('a', $first->column1);
    }
}
