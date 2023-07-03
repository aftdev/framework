# Api Manager

The main philosophy of this package is that your openAPI specification is the
source of truth of your application, it is the contract provided to your
consumers, and thus can be used to generate other things like routes or request
validator.

## Configuration

```php
[
  'spec' => '/path/to/openapi.yml',
  'namespace' => 'App\Api\Controller',
  'mutations' => [],
  'versions' => [],
  'servers' => [
    'url' => 'https://api.server.com',
  ],
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

## Routing

This package comes with a router helper that will autogenerate route
configuration from an openapi spec object.

This configuration can then be used by your router of choice (like FastRouter or
Mezzio Application)

```php


```

### Route parameters

Each router have a different parameter format, by default our
OpenApiRouteGenerator is setup to use the FastRouter format.

To override this, simply create a new translator and inject it to the Route
Generator (via container dependency injection or manually)

```php
  'aliases' => [
      ParamTranslatorInterface::class => YourTranslator::class
  ],
```

## OpenAPI request validator via middleware (PSR-15 Middleware Factory)

This package provides a middleware factory that returns a PSR-15 compatible
middleware that will validate that all incoming requests matches the format
defined by your openapi spec.

This middleware also validate that whatever data is sent back from your
controllers match the schemas defined in the spec

The middleware uses the openapi validator from
https://github.com/thephpleague/openapi-psr7-validator

```php
$openApiRequestValidatorMiddleware = $container->get(\League\OpenAPIValidation\PSR15\ValidationMiddleware::class);

// Use middleware when defining your openapi routes
// Actual implementation will depending on your router.
```
