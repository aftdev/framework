<?php

declare(strict_types=1);

namespace AftDev\Test\Feature\Api;

use AftDev\Test\FeatureTestCase;
use League\OpenAPIValidation\PSR15\ValidationMiddleware;

/**
 * @internal
 *
 * @covers \AftDev\Api\Factory\RequestValidationMiddlewareFactory
 */
final class RequestValidationMiddlewareTest extends FeatureTestCase
{
    public function testFastRouter()
    {
        $middleware = $this->container->get(ValidationMiddleware::class);

        $this->assertInstanceOf(ValidationMiddleware::class, $middleware);
    }
}
