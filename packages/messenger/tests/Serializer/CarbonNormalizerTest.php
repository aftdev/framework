<?php

namespace AftDevTest\Filesystem;

use AftDev\Messenger\Serializer\Normalizer\CarbonDenormalizer;
use AftDev\Test\TestCase;
use Carbon\Carbon;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * @covers \AftDev\Messenger\Serializer\Normalizer\CarbonDenormalizer
 *
 * @internal
 */
class CarbonNormalizerTest extends TestCase
{
    public function testDenormalizer()
    {
        $denormalizer = new CarbonDenormalizer();

        $test = $denormalizer->denormalize('2020-01-02', 'type');
        $this->assertInstanceOf(Carbon::class, $test);
    }

    public function testDenormalizerException()
    {
        $denormalizer = new CarbonDenormalizer();

        $this->expectException(NotNormalizableValueException::class);
        $test = $denormalizer->denormalize('fasdfasdf', 'type');
    }
}
