<?php

namespace AftDevTest\Api;

use AftDev\Api\Exception\UnknownVersionException;
use AftDev\Api\OpenApiManager;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use cebe\openapi\spec\OpenApi;
use Prophecy;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Api\OpenApiManager
 */
final class OpenApiManagerTest extends TestCase
{
    use Prophecy\PhpUnit\ProphecyTrait;

    public function testUnknownVersion()
    {
        $this->expectException(UnknownVersionException::class);

        $openApiManager = new OpenApiManager('specFile', [
            '2018-01-01' => [],
            '2011-01-01' => [],
            '2012-01-01' => [],
        ]);

        $openApiManager->getVersion('v1');
    }

    public function testUnknownSpec()
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('File does not exists');

        $openApiManager = new OpenApiManager('invalid.file');

        $openApiManager->getCurrentVersion();
    }

    public function testUnknownExtension()
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('Invalid file extension');

        $openApiManager = new OpenApiManager(realpath(__DIR__.'/specs/not-supported.php'));

        $openApiManager->getCurrentVersion();
    }

    public function testVersions()
    {
        $openApiManager = new OpenApiManager(
            realpath(__DIR__.'/specs/petstore.yaml'),
            [
                '2018-01-01' => [],
                '2011-01-01' => [],
                '2012-01-01' => [],
            ]
        );

        $versions = $openApiManager->getVersions();

        $this->assertEquals([
            '2018-01-01',
            '2012-01-01',
            '2011-01-01',
        ], $versions);
    }

    public function testVersionMutations()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $resolver = new Resolver($container->reveal());

        $openApiManager = new OpenApiManager(
            realpath(__DIR__.'/specs/petstore.yaml'),
            [
                '2018-01-01' => [],
                '2011-01-01' => [
                    fn (OpenApi $openApi) => $openApi->info->description .= '|Mutation2011A',
                    fn (OpenApi $openApi) => $openApi->info->description .= '|Mutation2011B',
                ],
                '2012-01-01' => [
                    fn (OpenApi $openApi) => $openApi->info->description .= '|Mutation2012',
                ],
            ],
            resolver: $resolver
        );

        $openApi = $openApiManager->getVersion('2011-01-01');

        $this->assertEquals('2011-01-01', $openApi->info->version);
        $this->assertEquals(
            'description_value|Mutation2012|Mutation2011A|Mutation2011B',
            $openApi->info->description
        );

        $openApi = $openApiManager->getVersion('2012-01-01');

        $this->assertEquals('2012-01-01', $openApi->info->version);
        $this->assertEquals(
            'description_value|Mutation2012',
            $openApi->info->description
        );
    }

    public function testJsonSpec()
    {
        $openApiManager = new OpenApiManager(realpath(__DIR__.'/specs/petstore.json'));
        $openApi = $openApiManager->getCurrentVersion();

        $this->assertEquals(
            'description_value_json',
            $openApi->info->description
        );
    }

    public function testVersionCache()
    {
        $openApiManager = new OpenApiManager(
            realpath(__DIR__.'/specs/petstore.yaml'),
            ['2012-01-01' => []],
        );

        $openApi = $openApiManager->getVersion('2012-01-01');
        $openApi2 = $openApiManager->getVersion('2012-01-01');

        $this->assertSame($openApi, $openApi2);
    }

    public function testMutatationsNoContainer()
    {
        $openApiManager = new OpenApiManager(
            realpath(__DIR__.'/specs/petstore.yaml'),
            [
                '2012-01-01' => [
                    fn (OpenApi $openApi) => $openApi->info->description .= '|Mutation2012',
                ],
            ]
        );

        $openApi = $openApiManager->getVersion('2012-01-01');
        $this->assertEquals(
            'description_value|Mutation2012',
            $openApi->info->description
        );
    }

    public function testNoVersion()
    {
        $openApiManager = new OpenApiManager(realpath(__DIR__.'/specs/petstore.yaml'));
        $openApi = $openApiManager->getCurrentVersion();

        $this->assertEquals(
            'description_value',
            $openApi->info->description
        );
    }

    public function testGetCurrentVersion()
    {
        $openApiManager = new OpenApiManager(
            realpath(__DIR__.'/specs/petstore.yaml'),
            versions: [
                'test-version' => [
                    fn (OpenApi $openApi) => $openApi->info->version = 'test-version',
                ],
            ],
        );
        $openApiManager->setCurrentVersion('test-version');

        $openApi = $openApiManager->getCurrentVersion();
        $this->assertEquals(
            'test-version',
            $openApi->info->version
        );
    }
}
