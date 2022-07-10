<?php

namespace AftDevTest\ServiceManager;

use AftDev\ServiceManager\Factory\ResolverAbstractFactory;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use Laminas\ServiceManager\ServiceManager;

/**
 * @internal
 * @covers \AftDev\ServiceManager\Resolver
 */
class ResolverTest extends TestCase
{
    protected ServiceManager $container;

    protected Resolver $resolver;

    public function setUp(): void
    {
        $container = new \Laminas\ServiceManager\ServiceManager([
            'services' => [
                ExistingServiceB::class => new ExistingServiceB(),
                ExistingServiceC::class => new ExistingServiceC(),
            ],
            'factories' => [
                Resolver::class => Resolver\ResolverFactory::class,
            ],
            'abstract_factories' => [
                ResolverAbstractFactory::class,
            ],
        ]);

        $this->resolver = $container->get(Resolver::class);
        $this->container = $container;
    }

    public function testResolveClass()
    {
        $this->resolver->when(TestService::class)->needs('optionsA')->give('options');
        $this->resolver->when(TestService::class)->needs('optionsB')->give(['a', 'b']);
        $this->resolver->when(TestService::class)->needs('optionsC')->give(1);
        $this->resolver->when(TestService::class)->needs('optionCallable')->give(function () {
            return 'fromCallable';
        });

        $testClass = $this->resolver->resolveClass(TestService::class);

        $this->assertSame($this->container->get(ExistingServiceB::class), $testClass->serviceB);
        $this->assertSame('options', $testClass->optionsA);
        $this->assertSame(['a', 'b'], $testClass->optionsB);
        $this->assertSame(1, $testClass->optionsC);
        $this->assertSame('fromCallable', $testClass->optionCallable);

        // Test type-hint Override.
        $overrideServiceB = new ExistingServiceB();
        $this->resolver->when(TestService::class)->needs(ExistingServiceB::class)->give($overrideServiceB);

        $withOverride = $this->resolver->resolveClass(TestService::class);
        $this->assertSame($overrideServiceB, $withOverride->serviceB);
    }

    public function testMultipleType()
    {
        $serviceB = $this->container->get(ExistingServiceB::class);
        $this->resolver->when(TestServiceComplex::class)->needs('intOrArray')->give(1);

        $testClass = $this->resolver->resolveClass(TestServiceComplex::class);

        $this->assertSame($serviceB, $testClass->serviceB);
        $this->assertSame(1, $testClass->intOrArray);
        $this->assertSame($serviceB, $testClass->intOrExistingServiceB);
    }

    /**
     * Test creation when no constructor or no parameters.
     */
    public function testNoConstructorAndConstructorWithoutParams()
    {
        $service = $this->resolver->resolveClass(ExistingServiceB::class);
        $this->assertInstanceOf(ExistingServiceB::class, $service);

        $serviceNoParams = $this->resolver->resolveClass(ExistingServiceC::class);
        $this->assertInstanceOf(ExistingServiceC::class, $serviceNoParams);
    }

    public function testCallFunction()
    {
        $serviceC = new ExistingServiceC();
        $container = new \Laminas\ServiceManager\ServiceManager([
            'services' => [
                ExistingServiceC::class => $serviceC,
            ],
            'factories' => [
                Resolver::class => Resolver\ResolverFactory::class,
            ],
            'abstract_factories' => [
                ResolverAbstractFactory::class,
            ],
        ]);

        $this->resolver = $container->get(Resolver::class);

        $testClass = $this->resolver->resolveClass(ExistingServiceB::class);

        $returnValue = $this->resolver->call([$testClass, 'handle'], ['options' => 'A']);

        $this->assertSame([
            'service' => $serviceC,
            'options' => 'A',
            'default' => 'default',
        ], $returnValue);

        $function = function (ExistingServiceC $service, string $options, string $default = 'default') {
            return [
                'service' => $service,
                'options' => $options,
                'default' => $default,
            ];
        };

        $returnValue = $this->resolver->call($function, ['options' => 'B']);
        $this->assertSame([
            'service' => $serviceC,
            'options' => 'B',
            'default' => 'default',
        ], $returnValue);

        // Fancy '@' notation to resolve class and function dependencies at the same time.
        $returnValue2 = $this->resolver->call(ExistingServiceB::class.'@handle', ['options' => '@@']);
        $this->assertSame([
            'service' => $serviceC,
            'options' => '@@',
            'default' => 'default',
        ], $returnValue2);

        $returnValue3 = $this->resolver->call(ExistingServiceC::class, ['test' => 'A']);
        $this->assertSame([
            'test' => 'A',
        ], $returnValue3);
    }

    /**
     * Test that the resolver will throw an exception.
     */
    public function testUnknownDependency()
    {
        $this->expectException(\ReflectionException::class);
        $this->resolver->resolveClass(NotAutodiscoverable::class);
    }

    public function testNoType()
    {
        $this->expectException(\ReflectionException::class);
        $this->resolver->resolveClass(NoType::class);
    }
}

class TestService
{
    public function __construct(
        public ExistingServiceB $serviceB,
        public string $optionsA,
        public array $optionsB,
        public int $optionsC,
        public string $optionsD = 'mooh',
        public $optionCallable = null
    ) {
    }
}

class TestServiceComplex
{
    public function __construct(
        public ExistingServiceB $serviceB,
        public int|array $intOrArray,
        public int|ExistingServiceB $intOrExistingServiceB,
    ) {
    }
}

class ExistingServiceB
{
    public function handle(ExistingServiceC $service, string $options, string $default = 'default')
    {
        return [
            'service' => $service,
            'options' => $options,
            'default' => $default,
        ];
    }
}

class ExistingServiceC
{
    public function __construct()
    {
    }

    public function __invoke(string $test)
    {
        return [
            'test' => $test,
        ];
    }
}

class NotAutodiscoverable
{
    public function __construct(array $test)
    {
    }
}

class NoType
{
    public function __construct($test)
    {
    }
}
