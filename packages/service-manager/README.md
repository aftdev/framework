# Service Manager

This package contains extra goodies built on top of the
[laminas service manager](https://docs.laminas.dev/laminas-servicemanager/).

## Configurable Service Manager

Extends the Laminas Plugin Manager. Allows definition of plugin
configuration/settings. To easily create configurable services/plugins.

```php
<?php
$config = [
    'manager_x' => [
        'default' => 'adapter_1',
        'default_options' => [
            'option_default' => 'Default',
        ],
        'plugins' => [
            'adapter_1' => [
                'service' => AdapterClass::class,  // What service to use,
                'options' => [
                    'option_1' => 'Option1',
                    'option_2' => 'Option2',
                ],
            ],
            'adapter_2' => [
                'service' => OtherAdapterClass::class,
                'options' => [
                    'option_1' => 'Option1',
                    'option_2' => 'Option2',
                ],
            ],
            // Short notation. (key is the name of the service to use)
            'service_name' => [
                'option_1' => 'Option1',
            ]
        ],
        // Laminas Plugin service manager configuration like factories or aliases.
        'factories' => [
            AdapterClass::class => Invokable::class,
            OtherAdapterClass::class => Invokable::class,
        ],
        'aliases' => [
            'service_name' => AdapterClass::class,
        ],
    ],
];
```

```php
class YourManager extends AbstractManager {

}

$pluginManager = new YourManager($container, $config['manager_x']);
$pluginManager->get('adapter_2');
```

## Resolver

### Service Resolver

This service automatically resolve services dependencies.

```php
$container = new class implements Psr\Container\ContainerInterface;

$resolver = new Resolver($container);

// With closures.
$resolver->call(function (Dependency $dependency) {
    $dependency->doSomething();
});

// With classes.
class ServiceA
{
    protected $dependencyA;

    public function __construct(DependencyA $dependencyA)
    {
        $this->dependencyA = $dependencyA;
    }
}

// Instantiate class only
$serviceA = $resolver->resolveClass(ServiceA::class);
```

### Resolver Abstract Factory

A Laminas abstract factory that will auto inject dependencies of your services
by using the resolver.

Add it to your laminas service manager configuration in the 'abstract_factories'
section. That way any unregistered services will be automatically created with
all of their dependencies injected.

```php
<?php

use AftDev\ServiceManager\ResolverAbstractFactory;

$service_manager_config = [
    'factories' => [],
    'aliases' => [],
    'abstract_factories' => [
        'default' => ResolverAbstractFactory::class,
    ],
];
```

Note: This factory is automatically added if you are using this package service
ConfigProvider.

### Contextual Binding / Binding Primitives

Primitives variables cannot be auto-discovered by the service manager and thus
would require context binding.

```php

class ServiceA
{
  public function __construct(
    private DependencyClass $dependency,
    private array $primitiveArray,
    private int $primitiveInt
  )
}

$resolver->when(ServiceA::class)->needs(DependencyClass::class)->give( new DependencyClass())
$resolver->when(ServiceA::class)->needs('primitiveArray')->give(['a','b','c'])
$resolver->when(ServiceA::class)->needs('primitiveInt')->give(1)


$serviceA = $resolver->resolveClass(ServiceA::class);

$serviceA->dependency; // DependencyClass
$serviceA->primitiveArray; // ['a','b','c']
$serviceA->primitiveInt; // 1
```

### Method Invocation & Injection

Use the Resolver to automatically inject dependencies when calling functions.
This is based on the laravel container
[functionality](https://laravel.com/docs/9.x/container#method-invocation-and-injection).

```php
$resolver->call(function(Dependency $dependency) {
  return $dependency->doSomething();
});
```

Automatically fetch a service from the container and invoke its function
(default is \_\_invoke) but you can customize the function to use:

```php
class ServiceA
{
    protected $dependencyA;

    public function __construct(DependencyA $dependencyA)
    {
        $this->dependencyA = $dependencyA;
    }

    public function __invoke(DependencyB $dependencyB)
    {
        $this->dependencyA->doSomething();
        $dependencyB->doSomething();
    }

    public function handle(DependencyC $dependencyC)
    {
        // ...
    }
}


$resolver->call(ServiceA::class); // uses __invoke
$resolver->call([ServiceA::class, 'handle']); // uses handle
$resolver->call(ServiceA::class.'@handle'); // uses handle
```

### PSR-15 Resolve Middleware

If your application uses PSR-15 Middleware - like
[Mezzio](https://docs.mezzio.dev/mezzio/) you could potentially use the provided
Resolve Middleware to automatically inject dependencies in your handlers
constructor but also handler actions.

Example when using the Mezzio router.

```php
// config/routes.php

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {

  // By Manually creating the middleware
  $resolver = $container->get(Resolver::class);
  $pingMiddleware = new ResolveMiddleware($container, App\Handler\PingHandler::class.'@myCustomAction');

  $app->get('/api/ping', $pingMiddleware, 'api.ping');

  // Or by using the factory

  $resolveMiddlewareFactory = $container->get(ResolveMiddlewareFactory::class);

  $app->get('/api/ping', $resolveMiddlewareFactory->prepare(App\Handler\PingHandler::class.'@otherAction'));
}
```

Note: By using this middleware, your handlers will not be able to implements the
`Psr\Http\Server\RequestHandlerInterface` anymore. They should nonetheless
return a `Psr\Http\Message\ResponseInterface`

```php
use Psr\Http\Message\ResponseInterface;

class PingHandler
{
  public function myCustomAction(DependencyOne $dep1, DependencyTwo $dep2): ResponseInterface
  {
  }

  public function otherAction(DependencyThree $dep3): ResponseInterface
  {
  }
}
```
