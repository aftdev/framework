<?php

namespace AftDev\Test\Feature\Api;

use AftDev\Api\ConfigProvider;
use AftDev\Api\OpenApiManager;
use AftDev\Test\FeatureTestCase;
use cebe\openapi\spec\OpenApi;

/**
 * @internal
 * @coversDefaultClass \AftDev\Api\OpenApiManager
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
        $this->overrideConfig(join('.', [ConfigProvider::CONFIG_KEY, 'versions']), ['zz' => [], 'test-version' => [
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
        $this->overrideConfig(join('.', [ConfigProvider::CONFIG_KEY, 'versions']), ['2019-01-02' => [
            function (OpenApi $openApi, OpenApiManager $apiManager) use (&$apiManagerClass) {
                $apiManagerClass = get_class($apiManager);
            },
        ]]);

        $openApiManager = $this->container->get(OpenApiManager::class);

        $openApi = $openApiManager->getVersion('2019-01-02');

        $this->assertEquals(OpenApiManager::class, $apiManagerClass);
    }
}
