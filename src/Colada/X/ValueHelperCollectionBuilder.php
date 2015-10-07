<?php

namespace Colada\X;

use ReflectionFunction;
use InvalidArgumentException;
use Colada\X\Helpers\StringyHelper;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class ValueHelperCollectionBuilder
{
    private $helpers;

    public function __construct()
    {
        $this->helpers = array();
    }

    /**
     * Register all class static method as helpers (except magic methods) as helpers.
     *
     * Example:
     * <code>
     * $builder->registerClassMethods('StringyHelper\\StaticStringy');
     * </code>
     *
     *
     * @param string|object $class Class name or object of this class.
     * @param array $methods Concrete methods.
     *
     * @return static When specified class does not exist.
     */
    public function registerClassMethods($class, $methods = null)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!class_exists($class)) {
            throw new InvalidArgumentException('Specified class does not exist.');
        }

        // TODO Take only static methods for classes and not static for objects.
        if (empty($methods)) {
            // Without magic methods.
            $methods = array_filter(get_class_methods($class), function ($method) {
                return !(0 === strpos($method, '__'));
            });
        }
        $helpers = array();
        foreach ($methods as $method) {
            $helpers[$method] = array($class, $method);
        }

        $this->helpers = array_merge($this->helpers, $helpers);

        return $this;
    }

    /**
     * Register StaticStringy (StringyHelper 2.1+) static methods as helpers.
     */
    public function registerStringyHelper()
    {
        $this->registerClassMethods(StringyHelper::getClass(), StringyHelper::getMethods());

        return $this;
    }

    /**
     * Register all functions in the given namespace as helpers.
     *
     * Example:
     * <code>
     * $builder->registerNamespaceFunctions('Functional');
     * </code>
     *
     * @param string $namespace Without slashes in the end!
     *
     * @return static
     */
    public function registerNamespaceFunctions($namespace)
    {
        // Remove starting slash, if exists.
        if (strpos($namespace, '\\') === 0) {
            $namespace = substr($namespace, 1);
        }

        // Remove magic methods.
        $functions = get_defined_functions();
        $functions = array_map(
            function ($functionName) { return new ReflectionFunction($functionName); },
            $functions['user']
        );

        $helpers = array();
        /** @var ReflectionFunction $function */
        foreach ($functions as $function) {
            if ($function->getNamespaceName() === $namespace) {
                // Strict
                $helpers[$this->camelize($function->getShortName())] = '\\' . $function->getName();
            }
        }

        $this->helpers = array_merge($this->helpers, $helpers);

        return $this;
    }

    /**
     * Register simple callback as helper.
     *
     * Examples:
     * <code>
     * // Register PHP's built in function trim() as helper.
     * $builder->register('trim');
     *
     * // Register closure as helper.
     * $builder->register(function ($string) { return explode(',', $string); }, 'splitByCommas');
     * </code>
     *
     * @throws InvalidArgumentException 1) If handler is not callable. 2) If name is empty and could not be determined.
     *
     * @param callback $callback
     * @param string $name
     *
     * @return static
     */
    public function register($callback, $name = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Handler is not callable.');
        }

        // Try to determine name.
        if (empty($name)) {
            if (is_string($callback)) {
                $parts = explode('\\', $callback);
                $name = $this->camelize(end($parts));
            } elseif (is_array($callback)) {
                // Method name.
                $name = $this->camelize($callback[1]);
            }
        }

        if (empty($name)) {
            throw new InvalidArgumentException('Name is empty and could not be determined.');
        }

        $this->helpers[$name] = $callback;

        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        return $this->helpers;
    }

    private function camelize($scored)
    {
        return lcfirst(
            implode(
                '',
                array_map(
                    'ucfirst',
                    array_map(
                        'strtolower',
                        explode('_', $scored)
                    )
                )
            )
        );
    }
}
