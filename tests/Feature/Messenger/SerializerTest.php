<?php

namespace AftDev\Test\Feature\Messenger;

use AftDev\Messenger\Serializer\QueueSerializer;
use AftDev\Test\FeatureTestCase;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\Serializer as SymfonyMessengerSerializer;

/**
 * @internal
 *
 * @covers \AftDev\Messenger\ConfigProvider
 * @covers \AftDev\Messenger\Serializer\Normalizer\CarbonDenormalizer
 * @covers \AftDev\Messenger\Serializer\Normalizer\ObjectNormalizerFactory
 * @covers \AftDev\Messenger\Serializer\Normalizer\PropertyNormalizerFactory
 */
class SerializerTest extends FeatureTestCase
{
    /**
     * Make sure we can serialize and deserialize properly.
     *
     * @covers \AftDev\Messenger\Serializer\QueueSerializer
     * @covers \AftDev\Messenger\Serializer\QueueSerializerFactory
     */
    public function testQueueSerializer()
    {
        /** @var QueueSerializer $serializer */
        $serializer = $this->container->get(QueueSerializer::class);

        $serialized = $serializer->serialize(new ToSerialize(), 'json');
        $serializedArray = json_decode($serialized, true);

        $expected = [
            'public' => 'public',
            'dateField' => '1970-01-13T20:38:31+00:00',
            'carbonField' => '1970-01-13T20:38:31+00:00',
            'protectedValue' => 'protected',
            'privateValue' => 'private',
        ];
        $this->assertEquals($expected, $serializedArray);

        $unserialized = $serializer->deserialize($serialized, ToSerialize::class, 'json');

        $this->assertSame('public', $unserialized->public);
        $this->assertSame('protected', $unserialized->getProtectedValue());
        $this->assertSame('private', $unserialized->getPrivateValue());

        $this->assertInstanceOf(\DateTime::class, $unserialized->dateField);
        $this->assertInstanceOf(CarbonInterface::class, $unserialized->carbonField);
    }

    /**
     * @covers \AftDev\Messenger\Serializer\MessageSerializerFactory
     */
    public function testTransportSerializer()
    {
        /** @var SymfonyMessengerSerializer $serializer */
        $serializer = $this->container->get(SymfonyMessengerSerializer::class);
        $envelope = new Envelope(new ToSerialize());

        $serialized = $serializer->encode($envelope);
        $unserialized = $serializer->decode($serialized);

        $this->assertEquals($envelope->getMessage(), $unserialized->getMessage());
    }
}

class ToSerialize
{
    /** @var string */
    public $public = 'public';

    /** @var \DateTime */
    public $dateField;

    /** @var CarbonInterface */
    public $carbonField;

    /** @var string */
    protected $protectedValue = 'protected';

    /** @var string */
    private $privateValue = 'private';

    public function __construct()
    {
        $this->dateField = new \DateTime();
        $this->dateField->setTimestamp('1111111');

        $this->carbonField = Carbon::createFromTimestamp('1111111');
    }

    public function getProtectedValue()
    {
        return $this->protectedValue;
    }

    public function getPrivateValue()
    {
        return $this->privateValue;
    }
}
