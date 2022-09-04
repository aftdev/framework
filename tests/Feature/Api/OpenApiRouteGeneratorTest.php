<?php

declare(strict_types=1);

namespace AftDev\Test\Feature\Api;

use AftDev\Api\Route\OpenApiRouteGenerator;
use AftDev\Cache\CacheManager;
use AftDev\Test\FeatureTestCase;
use cebe\openapi\Reader;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @internal
 *
 * @covers \AftDev\Api\Route\OpenApiRouteGenerator
 */
final class OpenApiRouteGeneratorTest extends FeatureTestCase
{
    private CacheItemPoolInterface $cache;

    protected function setup(): void
    {
        parent::setup();

        $cacheManager = $this->container->get(CacheManager::class);
        $this->cache = $cacheManager->store('php');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cache->clear();
    }

    public function testCache()
    {
        $generator = new OpenApiRouteGenerator(cache: $this->cache);
        $spec = Reader::readFromYamlFile(realpath(__DIR__.'/../../../packages/api/tests/specs/routes.yaml'));

        $routes = $generator->getRoutes($spec);

        $this->assertIsArray($routes);

        $cache = $generator->getCache($spec);
        $this->assertTrue($cache->isHit());

        // When cache exists.
        $routes = $generator->getRoutes($spec);
        $this->assertIsArray($routes);
    }
}
