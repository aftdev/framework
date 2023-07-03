<?php

declare(strict_types=1);

namespace AftDev\Api\Route;

class Route
{
    public function __construct(
        public string $uri,
        public string $method,
        public string $name,
        public string $handler,
    ) {
    }
}
