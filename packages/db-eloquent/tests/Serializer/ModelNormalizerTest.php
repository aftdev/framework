<?php

namespace AftDevTest\DbEloquent\Middleware;

use AftDev\DbEloquent\Serializer\ModelNormalizer;
use AftDev\Test\TestCase;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * @covers \AftDev\DbEloquent\Serializer\ModelNormalizer
 *
 * @internal
 */
class ModelNormalizerTest extends TestCase
{
    public function testNormalizeException()
    {
        $normalizer = new ModelNormalizer();

        $this->expectException(InvalidArgumentException::class);
        $normalizer->normalize(new \stdClass());
    }

    /**
     * @dataProvider denormalizerErrorProvider
     *
     * @param mixed $data
     */
    public function testDenormalize($data)
    {
        $normalizer = new ModelNormalizer();

        $this->expectException(NotNormalizableValueException::class);
        $normalizer->denormalize($data, 'type');
    }

    public function denormalizerErrorProvider()
    {
        return [
            'array missing model' => [
                'data' => [
                    'id' => 1,
                ],
            ],
            'array missing id' => [
                'data' => [
                    'model' => 'a',
                ],
            ],
            'not array' => [
                'data' => 'string',
            ],
        ];
    }
}
