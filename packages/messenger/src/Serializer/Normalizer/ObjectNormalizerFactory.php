<?php

namespace AftDev\Messenger\Serializer\Normalizer;

use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ObjectNormalizerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        // This extracts the parameter types from the phpdoc value.
        $extractor = new PhpDocExtractor();

        return new ObjectNormalizer(null, null, null, $extractor);
    }
}
