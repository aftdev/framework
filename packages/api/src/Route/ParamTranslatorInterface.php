<?php

declare(strict_types=1);

namespace AftDev\Api\Route;

interface ParamTranslatorInterface
{
    public function translate(string $value, bool $required, string $type, ?string $format = null): string;
}
