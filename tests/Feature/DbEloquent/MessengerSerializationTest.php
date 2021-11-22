<?php

namespace AftDev\Test\Feature\DbEloquent;

use AftDev\DbEloquent\Capsule\CapsuleManager;
use AftDev\Messenger\Serializer\QueueSerializer;
use AftDev\Test\FeatureTestCase;
use AftDev\Test\Model\TestModel;

/**
 * @internal
 * @covers \AftDev\DbEloquent\ConfigProvider
 * @covers \AftDev\DbEloquent\Serializer\ModelNormalizer
 */
class MessengerSerializationTest extends FeatureTestCase
{
    protected $useDb = true;

    public function testEloquentModel()
    {
        /** @var CapsuleManager $capsuleManager */
        $capsuleManager = $this->container->get(CapsuleManager::class);
        $capsuleManager->bootEloquent();

        $model = new TestModel();
        $model->column1 = 'test test';
        $model->save();

        /** @var QueueSerializer $messageSerializer */
        $messageSerializer = $this->container->get(QueueSerializer::class);
        $message = new TestMessage($model);

        $serialized = $messageSerializer->serialize($message, 'json');

        $deserialized = $messageSerializer->deserialize($serialized, TestMessage::class, 'json');

        /** @var TestModel $deserializedModel */
        $deserializedModel = $deserialized->model;
        $this->assertInstanceOf(TestModel::class, $deserializedModel);
        $this->assertTrue($deserializedModel->exists);

        // Delete model row and check that we can still unserialize.
        $model->forceDelete();

        $deserializedWithDeletedRow = $messageSerializer->deserialize($serialized, TestMessage::class, 'json');

        $this->assertInstanceOf(TestModel::class, $deserializedWithDeletedRow->model);
        $this->assertFalse($deserializedWithDeletedRow->model->exists);
    }
}

class TestMessage
{
    /**
     * @var TestModel
     */
    public $model;

    public function __construct(TestModel $model)
    {
        $this->model = $model;
    }
}
