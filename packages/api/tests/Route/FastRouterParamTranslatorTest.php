<?php

declare(strict_types=1);

namespace AftDevTest\Api\Route;

use AftDev\Api\Route\FastRouterParamTranslator;
use AftDev\Test\TestCase;

/**
 * @internal
 *
 * @covers \AftDev\Api\Route\FastRouterParamTranslator
 */
final class FastRouterParamTranslatorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testTranslation(array $options, string $expected)
    {
        $translator = new FastRouterParamTranslator();

        $transformed = $translator->translate('/{id}', ...$options);
        $this->assertEquals($expected, $transformed);
    }

    public function dataProvider()
    {
        return [
            'string' => [
                'options' => [
                    'required' => true,
                    'type' => 'string',
                    'format' => '',
                ],
                'expected' => '/{id}',
            ],
            'string-optional' => [
                'options' => [
                    'required' => false,
                    'type' => 'string',
                    'format' => '',
                ],
                'expected' => '[/{id}]',
            ],
            'integer' => [
                'options' => [
                    'required' => true,
                    'type' => 'integer',
                    'format' => '',
                ],
                'expected' => '/{id:\d+}',
            ],
            'number' => [
                'options' => [
                    'required' => true,
                    'type' => 'number',
                    'format' => '',
                ],
                'expected' => '/{id}',
            ],
            'boolean' => [
                'options' => [
                    'required' => true,
                    'type' => 'boolean',
                    'format' => '',
                ],
                'expected' => '/{id:true|false}',
            ],
        ];
    }
}
