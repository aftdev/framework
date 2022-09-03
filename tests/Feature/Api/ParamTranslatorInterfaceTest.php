<?php

declare(strict_types=1);

namespace AftDev\Test\Feature\Api;

use AftDev\Api\Route\FastRouterParamTranslator;
use AftDev\Api\Route\ParamTranslatorInterface;
use AftDev\Test\FeatureTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ParamTranslatorInterfaceTest extends FeatureTestCase
{
    /**
     * @covers \AftDev\Api\Route\FastRouterParamTranslator
     */
    public function testFastRouter()
    {
        $translator = $this->container->get(ParamTranslatorInterface::class);

        $this->assertInstanceOf(ParamTranslatorInterface::class, $translator);
        $this->assertInstanceOf(FastRouterParamTranslator::class, $translator);
    }
}
