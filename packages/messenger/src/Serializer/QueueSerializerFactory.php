<?php

namespace AftDev\Messenger\Serializer;

use AftDev\Messenger\ConfigProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class QueueSerializerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $encoders = ['json' => new JsonEncoder()];

        $normalizers = $container->get('config')[ConfigProvider::KEY_MESSENGER][ConfigProvider::KEY_NORMALIZERS];
        $normalizersLoaded = [];

        foreach ($normalizers as $name => $normalizer) {
            $normalizersLoaded[] = $container->has($normalizer) ? $container->get($normalizer) : new $normalizer();
        }

        return new Serializer($normalizersLoaded, $encoders);
    }
}
