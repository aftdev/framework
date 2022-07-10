# Api Manager

The main philosophy of this package is that your openAPI specification is the
source of truth of your application, it is the contract provided to your
consumers, and thus can be used to generate other things like routes or request
validator.

## Configuration

```php
[
  'prefix': 'api',
  'spec' => '/path/to/openapi.yml',
  'namespace' => 'App/Api/Controller',
  'mutations' => [],
  'versions' => [],
]
```

## Versioning and Mutations

Versioning comes in handy when you introduce breaking changes. Our
recommendation is that every time it happens you would create a new version
based on a calendar date.

The main idea is that the spec file should always contains the most up to date
information and each version only contain mutations to make it backward
compatible.

Because the versions are in chronological order we can apply them recursively.

```php
[
  'spec' => '/path/to/openapi.yml',
  'current_version' => '2022-05-10',
  'versions' => [
    '2022-05-10' => [],
    '2017-01-03' => [
        MutationOne::class,
    ],
    '2016-11-24' => [
        MutationTwo::class,
    ],
    '2012-04-03' => [
        MutationThree::class,
    ],
  ],
];
```

e.g: to generate version `2016-11-03`, we would start from the `spec` file Then
apply all version rollbacks up to that version. In our example that means be
`MutationOne::class` and `MutationTwo::class`

Mutations can apply to any version so you could even add mutations to the
current versions This could be useful if you want to test a new feature or a
change before making it public.

```php
[
  'spec' => '/path/to/openapi.yml'
  'current_version' => '2022-05-10',
  'versions' => [
    '2022-05-10' => [
      ChangeSpecIfFeatureToggleIsOn::class
    ],
    // ...
  ],
]
```

Each mutations should be invokables classes. The OpenApi object will be passed
as argument so you can freely edit it.

example:

```php
use cebe\openapi\spec\OpenApi;

class ChangeSpecIfFeatureToggleIsOn implements OpenApiMutation
{
  public function __invoke(OpenApi $spec, FeatureToggleService $featureToggle): void
  {
    $toggleOn = $featureToggle->isEnabled('You Cool new feature');

    if (false === $toggleOn) {
      // Do nothing.
      return;
    }

    $spec->paths['/new-path'] = new PathItem([...]);
  }
}
```

Note: If configured properly, each mutation classes will be invoked via the
Service manager resolver. [TODO ADD LINK TO RESOLVER] It means that the class
dependencies will be automatically injected.

### Checking version in your controllers / business logic services.

From you controllers or other services you would just need to check the current
version via the `OpenApiManager->getVersion()` method.

```php
use cebe\openapi\spec\OpenApi;

class PathController
{
  public function __construct(
    private OpenApi $openApi,
  )
  {}

  public function __invoke()
  {
    if ($openApi->getVersion() > `2016-11-24`) {
      return ['a', 'b', 'c'];
    }

    return 'a';
  }
}
```

## Middlewares ([psr-15](https://www.php-fig.org/psr/psr-15/))

### OpenApiMiddleware

This middleware will setup the current version of the version being used for the
current request and inject the OpenApi object to the request.

## Handler ([psr-15](https://www.php-fig.org/psr/psr-15/))

### OpenApiPathHandler Abstract

This is the default handler that is injected in the router of your choice This
handler will

- make sure the incoming request is valid (using phpleague validator)
- call the appropirate controller action (depending on the openapi operation)
- make sure the outcoming response is valid (using phpleague validator)

#### MezzioApiHandler

## Routing

This package comes with a router helper that will autogenerate route
configuration from an openapi spec object.

This configuration can then be used by your router of choice (like fastRouter or
Mezzio Application) Service that return an array of routes and controllers /
params we need a mapper for each router type.

TODO - routes per version. (how to handle caching?)
