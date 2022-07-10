<?php

namespace AftDevTest\Api\Middleware;

use AftDev\Api\Factory\OpenApiManagerFactory;
use AftDev\Api\OpenApiManager;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 * @covers \AftDev\Api\Factory\OpenApiManagerFactory
 */
final class OpenApiManagerFactoryTest extends TestCase
{
    private ContainerInterface|ProphecyInterface $container;
    private Resolver|MockObject $resolver;
    private array $config = [];

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->resolver = $this->createStub(Resolver::class);

        $self = $this;
        $this->container->get('config')->will(fn () => $self->config);

        $this->container->has(Resolver::class)->willReturn(false);
        $this->container->has(ServerRequestInterface::class)->willReturn(false);
    }

    public function testWithResolver()
    {
        $this->config = [
            'api' => [
                'spec' => 'x',
                'versions' => [],
                'version' => null,
            ],
        ];

        $this->container->has(Resolver::class)->willReturn(true);
        $this->container->get(Resolver::class)->shouldBeCalled()->willReturn($this->resolver);

        (new OpenApiManagerFactory())($this->container->reveal());
    }

    public function testWithVersionFromHeader()
    {
        $this->config = [
            'api' => [
                'spec' => realpath(__DIR__.'/../specs/petstore.yaml'),
                'versions' => [
                    'from-header' => [],
                    'from-config' => [],
                ],
                'version' => 'from-config',
            ],
        ];

        $version = 'from-header';

        $request = new ServerRequest(headers: [OpenApiManager::VERSION_HEADER_NAME => $version]);
        $this->container->has(ServerRequestInterface::class)->willReturn(true);
        $this->container->get(ServerRequestInterface::class)->willReturn($request);

        $openApiManager = (new OpenApiManagerFactory())($this->container->reveal());

        $versionSpec = $openApiManager->getCurrentVersion();
        $this->assertEquals($versionSpec->info->version, $version);
    }

    public function testWithVersionFromHeaderCallback()
    {
        $this->config = [
            'api' => [
                'spec' => realpath(__DIR__.'/../specs/petstore.yaml'),
                'versions' => [
                    'from-header' => [],
                    'from-config' => [],
                ],
                'version' => 'from-config',
            ],
        ];

        $version = 'from-header';

        $request = new ServerRequest(headers: [OpenApiManager::VERSION_HEADER_NAME => $version]);
        $this->container->has(ServerRequestInterface::class)->willReturn(true);
        $this->container->get(ServerRequestInterface::class)->willReturn(fn () => $request);

        $openApiManager = (new OpenApiManagerFactory())($this->container->reveal());

        $versionSpec = $openApiManager->getCurrentVersion();
        $this->assertEquals($versionSpec->info->version, $version);
    }

    public function testWithVersionFromConfig()
    {
        $this->config = [
            'api' => [
                'spec' => realpath(__DIR__.'/../specs/petstore.yaml'),
                'versions' => [
                    'from-header' => [],
                    'from-config' => [],
                ],
                'version' => 'from-config',
            ],
        ];

        $openApiManager = (new OpenApiManagerFactory())($this->container->reveal());

        $versionSpec = $openApiManager->getCurrentVersion();
        $this->assertEquals($versionSpec->info->version, 'from-config');
    }

    public function testWithoutVersion()
    {
        $this->config = [
            'api' => [
                'spec' => realpath(__DIR__.'/../specs/petstore.yaml'),
            ],
        ];

        $openApiManager = (new OpenApiManagerFactory())($this->container->reveal());

        $versionSpec = $openApiManager->getCurrentVersion();
        $this->assertEquals($versionSpec->info->version, '1.0.0');
    }
}
