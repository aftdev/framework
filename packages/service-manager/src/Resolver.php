<?php

namespace AftDev\ServiceManager;

use AftDev\ServiceManager\Resolver\RuleBuilder;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

class Resolver
{
    /**
     * Array of rules on how to resolve parameters.
     */
    protected array $rules = [];

    public function __construct(
        protected ContainerInterface $container
    ) {
    }

    /**
     * Create a class with all dependencies automatically injected.
     *
     * @throws ReflectionException If a parameter cannot be resolved.
     */
    public function resolveClass(string $requestedName, array $params = []): object
    {
        $reflectionClass = new ReflectionClass($requestedName);
        $constructor = $reflectionClass->getConstructor();
        $constructorParams = $constructor ? $constructor->getParameters() : [];

        if (empty($constructorParams)) {
            return new $requestedName();
        }

        $parameterValues = array_merge($params, $this->rules[$requestedName] ?? []);
        $constructorParameters = array_map(
            fn (ReflectionParameter $parameter) => $this->mapParameter($parameter, $parameterValues),
            $constructorParams
        );

        return new $requestedName(...$constructorParameters);
    }

    /**
     * Automatically call a function with all parameters injected from the container.
     *
     * @param array|callable|string $function - The function name or an array class,function name.
     * @param array $parameters - List of hard coded values.
     *
     * @throws ReflectionException If a parameter cannot be resolved.
     */
    public function call(array|callable|string $function, array $parameters = []): mixed
    {
        // Check if callable
        if (is_callable($function)) {
            if (is_array($function)) {
                $reflection = new ReflectionMethod(...$function);
            } else {
                $reflection = new ReflectionFunction($function);
            }
        } else {
            $exploded = explode('@', $function);

            $className = $exploded[0];
            $functionName = $exploded[1] ?? '__invoke';

            $reflection = new ReflectionMethod($className, $functionName);
            $function = [$this->container->get($className), $functionName];
        }

        $reflectionParameters = $reflection->getParameters();

        $parameters = array_map(
            fn (ReflectionParameter $parameter) => $this->mapParameter($parameter, $parameters),
            $reflectionParameters
        );

        return call_user_func($function, ...$parameters);
    }

    /**
     * Create Rule builder for a service.
     */
    public function when(string $serviceName): RuleBuilder
    {
        return new RuleBuilder($serviceName, $this);
    }

    /**
     * Add a default value for a parameter.
     *
     * @param mixed $implementation
     */
    public function addServiceRule(string $serviceName, string $parameter, $implementation): void
    {
        $this->rules[$serviceName][$parameter] = $implementation;
    }

    /**
     * Map function parameter to the given list.
     *
     * @throws ReflectionException If parameter cannot be resolved.
     */
    protected function mapParameter(ReflectionParameter $parameter, array $parameters = [])
    {
        $parameterName = $parameter->getName();

        // Primitives.
        $primitiveName = $parameterName;
        if (array_key_exists($primitiveName, $parameters)) {
            return $this->getParameterValue($parameters[$primitiveName]);
        }

        // By TypeHint.
        $type = $parameter->hasType() ? $parameter->getType() : null;

        $types = $type
            ? ($type instanceof (\ReflectionUnionType::class) ? $type->getTypes() : [$type])
            : []
        ;

        foreach ($types as $type) {
            if ($type->isBuiltin()) {
                continue;
            }

            // Rule on type?
            $parameterTypeName = $type->getName();
            if (array_key_exists($parameterTypeName, $parameters)) {
                return $this->getParameterValue($parameters[$parameterTypeName]);
            }

            // Can the type be fetched from container?
            if ($this->container->has($parameterTypeName)) {
                return $this->container->get($parameterTypeName);
            }
        }

        // Finally check for default value.
        if (!$parameter->isOptional()) {
            throw new ReflectionException(
                message: sprintf(
                    'Unable to resolve parameter "%s"',
                    $parameterName
                ),
            );
        }

        return $parameter->getDefaultValue();
    }

    protected function getParameterValue($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}
