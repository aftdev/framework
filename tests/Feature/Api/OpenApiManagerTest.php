<?php

namespace AftDev\Test\Feature\Api;

use AftDev\Api\ConfigProvider;
use AftDev\Api\OpenApiManager;
use AftDev\Cache\CacheManager;
use AftDev\Test\FeatureTestCase;
use cebe\openapi\spec\OpenApi;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @internal
 *
 * @coversDefaultClass \AftDev\Api\OpenApiManager
 *
 * @covers \AftDev\Api\ConfigProvider
 * @covers \AftDev\Api\OpenApiManager
 */
final class OpenApiManagerTest extends FeatureTestCase
{
    /**
     * @covers \AftDev\Api\Factory\OpenApiManagerFactory
     */
    public function testManager()
    {
        $openApiManager = $this->container->get(OpenApiManager::class);

        $this->assertInstanceOf(OpenApiManager::class, $openApiManager);
    }

    /**
     * @covers \AftDev\Api\Factory\CurrentOpenApiVersionFactory
     */
    public function testCurrentOpenApi()
    {
        /** @var OpenApi $openApi */
        $openApi = $this->container->get(OpenApi::class);

        $this->assertInstanceOf(OpenApi::class, $openApi);
        $this->assertEquals('Swagger Petstore', $openApi->info->title);
        $this->assertEquals('1.0.0', $openApi->info->version);
    }

    /**
     * @covers \AftDev\Api\Factory\CurrentOpenApiVersionFactory
     * @covers \AftDev\Api\Factory\OpenApiManagerFactory
     */
    public function testCurrentVersion()
    {
        $this->overrideConfig([ConfigProvider::CONFIG_KEY, 'versions'], ['zz' => [], 'test-version' => [
            fn (OpenApi $openApi) => $openApi->info->version = 'test-version',
        ]]);
        $this->overrideConfig(join('.', [ConfigProvider::CONFIG_KEY, 'version']), 'test-version');

        $openApi = $this->container->get(OpenApi::class);

        $this->assertInstanceOf(OpenApi::class, $openApi);
        $this->assertEquals('test-version', $openApi->info->version);
    }

    public function testMutationDependenciesInjection()
    {
        $apiManagerClass = false;
        $this->overrideConfig([ConfigProvider::CONFIG_KEY, 'versions'], ['2019-01-02' => [
            function (OpenApi $openApi, OpenApiManager $apiManager) use (&$apiManagerClass) {
                $apiManagerClass = get_class($apiManager);
            },
        ]]);

        $openApiManager = $this->container->get(OpenApiManager::class);

        $openApi = $openApiManager->getVersion('2019-01-02');

        $this->assertEquals(OpenApiManager::class, $apiManagerClass);
    }

    /**
     * @covers \AftDev\Api\Factory\OpenApiManagerFactory
     */
    public function testCache()
    {
        $this->overrideConfig([ConfigProvider::CONFIG_KEY, 'cache'], 'php');
        $this->overrideConfig([ConfigProvider::CONFIG_KEY, 'versions'], ['2019-01-02' => []]);

        $cacheManager = $this->container->get(CacheManager::class);

        /** @var CacheItemPoolInterface|\Symfony\Component\Cache\Adapter\PhpFilesAdapter $phpStore */
        $phpStore = $cacheManager->get('php');
        $phpStore->clear();

        $openApiManager = $this->container->get(OpenApiManager::class);
        $openApiManager->getCurrentVersion();

        $this->assertTrue($phpStore->hasItem('api.specs._base'));

        return $phpStore;
    }

    /**
     * Make sure the cache is being reused.
     *
     * @depends testCache
     *
     * @param mixed $phpStore
     */
    public function testCacheFound($phpStore)
    {
        $this->overrideConfig([ConfigProvider::CONFIG_KEY, 'cache'], 'php');
        $this->overrideConfig([ConfigProvider::CONFIG_KEY, 'versions'], ['2019-01-02' => []]);

        $cacheManager = $this->container->get(CacheManager::class);
        $phpStore = $cacheManager->get('php');
        $this->assertTrue($phpStore->hasItem('api.specs._base'));

        $openApiManager = $this->container->get(OpenApiManager::class);
        $spec = $openApiManager->getCurrentVersion();

        $phpStore->clear();
        $this->assertInstanceOf(OpenApi::class, $spec);
    }
}
