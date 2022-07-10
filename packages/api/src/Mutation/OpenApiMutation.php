<?php

namespace AftDev\Api\Mutations;

use cebe\openapi\spec\OpenApi;

interface OpenApiMutation
{
    /**
     * Mutate the openapi spec.
     */
    public function __invoke(OpenApi $openApi): void;
}
