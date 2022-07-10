<?php

namespace AftDev\Cache\Factory;

use AftDev\Cache\CacheManager;
use AftDev\ServiceManager\Factory\ResolverAbstractFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class TagAwareAdapterFactory extends ResolverAbstractFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];

        /** @var CacheManager $cacheManager */
        $cacheManager = $container->get(CacheManager::class);

        $itemsPoolName = $options['itemsPool'] ?? false;

        if (!$itemsPoolName) {
            throw new ServiceNotCreatedException('[itemsPool] options is missing.');
        }

        $itemsPoolsAdapter = $cacheManager->store($itemsPoolName);

        $tagsPoolName = $options['tagsPool'] ?? [];
        $tagsPoolAdapter = $tagsPoolName ? $cacheManager->store($tagsPoolName) : $itemsPoolsAdapter;

        return new TagAwareAdapter($itemsPoolsAdapter, $tagsPoolAdapter, $options['knownTagVersionsTtl'] ?? 0.15);
    }
}
