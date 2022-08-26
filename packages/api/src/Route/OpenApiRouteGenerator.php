<?php

declare(strict_types=1);

namespace AftDev\Api\Route;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\PathItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class OpenApiRouteGenerator
{
    public const CACHE_NAME = 'api.routes.%s';

    private $methodMapping = [
        'get' => 'index', // when no path params.
        'getWithParams' => 'show', // where there is a param
        'post' => 'create',
        'put' => 'update',
        'patch' => 'update',
    ];

    public function __construct(
        private $prefix = '/api',
        private $namespace = 'App\Controller',
        private ?CacheItemPoolInterface $cache = null,
    ) {
    }

    public function getCache($openApi): ?CacheItemInterface
    {
        $version = $openApi->info->version ?? 'default';
        $sanitized = preg_replace('/[^a-zA-Z0-9]+/', '', $version);

        return $this->cache ? $this->cache->getItem(sprintf(self::CACHE_NAME, $sanitized)) : null;
    }

    /**
     * Get routes form the given spec.
     *
     * Will fetch from cache if it exists.
     */
    public function getRoutes(OpenApi $openApi): array
    {
        $routeCache = $this->getCache($openApi);
        if ($routeCache && $routeCache->isHit()) {
            return $routeCache->get();
        }

        $generator = $this->generateRoutes($openApi);
        $routeArray = iterator_to_array($generator, false);

        if ($routeCache) {
            $routeCache->set($routeArray);
            $this->cache->save($routeCache);
        }

        return $routeArray;
    }

    /**
     * Returns routes from the openapi spec.
     */
    public function generateRoutes(OpenApi $openApi): \Generator
    {
        foreach ($openApi->paths as $path => $pathItem) {
            yield from $this->getRoutesForPath($path, $pathItem);
        }
    }

    private function prefixUri(string $path): string
    {
        return Str::of($path)->start($this->prefix)->start('/')->toString();
    }

    private function getRoutesForPath(string $uri, PathItem $path): \Generator
    {
        $pathCommonParams = [
            'uri' => $this->prefixUri($uri),
        ];

        $pathParams = $this->getPathParameters($path->parameters);

        foreach ($path->getOperations() as $method => $operation) {
            yield $pathCommonParams + [
                'method' => $method,
                'name' => $operation->operationId ?? 'api.'.Str::camel("{$method}{$uri}"),
                'params' => $pathParams + $this->getPathParameters($operation->parameters),
                'handler' => $this->getHandlerNameForOperation($uri, $method),
            ];
        }
    }

    private function getPathParameters(array $parameters)
    {
        $pathParams = array_filter($parameters, fn ($param) => 'path' == $param->in);
        $paramInfo = [];
        foreach ($pathParams as $param) {
            $paramInfo[$param->name] = [
                'required' => $param->required,
                'schema' => [
                    'type' => $param->schema->type,
                    'format' => $param->schema->format,
                ],
            ];
        }

        return $paramInfo;
    }

    private function getHandlerNameForOperation(string $path, string $method): string
    {
        $pathInfo = Str::of($path)->trim('/')->explode('/');

        $routeParams = $pathInfo->filter(fn ($str) => Str::match('/^\{.+\}$/', $str));
        $routeResources = $pathInfo->diff($routeParams);

        $controllerPosition = $routeResources->keys()->last();
        $routeParams = $routeParams->skipWhile(fn ($i, $key) => $key <= $controllerPosition);

        // Controller is always the last nonParam
        $controller = $this->getControllerForPath((string) $routeResources->pop());
        $methodName = $this->getMethodName($method, $routeResources, $routeParams, $controller);

        return $controller.'@'.$methodName;
    }

    private function getControllerForPath(string $controller): string
    {
        return $this->namespace.'\\'.Str::of($controller)->ucfirst()->pluralStudly();
    }

    private function getMethodName(string $method, Collection $routeResources, Collection $routeParams, string $controller): string
    {
        $hasParams = $routeParams->count();

        // From Mapping
        $verb = ($hasParams && isset($this->methodMapping[$method.'WithParams']) ? $this->methodMapping[$method.'WithParams'] : null)
        ?? $this->methodMapping[$method]
        ?? $method;

        $scopedBy = $routeResources->map(fn ($s) => Str::of($s)->singular()->studly()->ucfirst());
        $filterBy = $routeParams->map(fn ($s) => Str::of($s)->trim('{}')->ucfirst());

        $methodName = $verb;
        $glue = 'By';

        if ($scopedBy->count()) {
            $methodName .= $glue.$scopedBy->join('');
            $glue = 'And';
        }

        // If we have only one param do not use it if it matches the controller name
        $appendFilterBy =
            $hasParams
            && (1 !== $hasParams || !$this->paramMatchesController($filterBy->first(), $controller));

        if ($appendFilterBy) {
            $methodName .= $glue.$filterBy->join('');
        }

        return $methodName;
    }

    private function paramMatchesController($param, $controller): bool
    {
        $potentialMatches = [
            'id',
            'uuid',
            $controller,
            $controller.'Id',
            $controller.'Uuid',
        ];

        return Str::contains(
            needles: $potentialMatches,
            haystack: $param,
            ignoreCase: true,
        );
    }
}
