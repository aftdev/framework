<?php

declare(strict_types=1);

namespace AftDev\Api\Route;

class FastRouterParamTranslator implements ParamTranslatorInterface
{
    private $mapping = [
        'string' => '',
        'integer' => '\d+',
        'number' => '',
        'boolean' => 'true|false',
    ];

    public function translate(string $value, bool $required, string $type, ?string $format = null): string
    {
        $name = trim($value, '/{}');

        $reg = $this->mapping[$type] ?? false;
        if ($reg) {
            $name .= ':'.$reg;
        }

        if (!$required) {
            return sprintf('[/{%s}]', $name);
        }

        return sprintf('/{%s}', $name) ?: $value;
    }
}
