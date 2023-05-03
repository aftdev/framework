<?php

namespace AftDev\Test\Feature\DbEloquent;

use AftDev\DbEloquent\Capsule\CapsuleManager;
use AftDev\Test\FeatureTestCase;
use AftDev\Test\Model\TestModel;

/**
 * @internal
 *
 * @covers \AftDev\DbEloquent\Capsule\CapsuleManager
 */
class DbEloquentTest extends FeatureTestCase
{
    protected $useDb = true;

    public function setUp(): void
    {
        parent::setUp();

        /** @var CapsuleManager $capsuleManager */
        $capsuleManager = $this->container->get(CapsuleManager::class);
        $capsuleManager->bootEloquent();
    }

    public function testEloquentModel()
    {
        $test = new TestModel();
        $test->column1 = 'test test';

        $test->save();

        $value = TestModel::all()->first();

        $this->assertEquals('test test', $value->column1);
    }
}
