<?php

namespace Colada\X;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class ValueHelperCollection implements \ArrayAccess
{
    private $helpers = array();

    /**
     * @var static
     */
    private static $instance;

    private function __construct()
    {

    }

    /**
     * Singleton method for working with default instance.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->helpers[$offset]);
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->helpers[$offset];
    }

    /**
     * @param string $offset
     * @param callable $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->register($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->helpers[$offset]);
    }


    /**
     * Register all class static method as helpers (except magic methods) as helpers.
     *
     * Example:
     * <code>
     * $collection->registerClassMethods('Stringy\\StaticStringy');
     * </code>
     *
     * @throws \InvalidArgumentException When specified class does not exist.
     *
     * @param string|object $class Class name or object of this class.
     *
     * @return static
     */
    public function registerClassMethods($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!class_exists($class)) {
            throw new \InvalidArgumentException('Specified class does not exist.');
        }

        // Remove magic methods.
        $methods = array_filter(get_class_methods($class), function($method) { return !preg_match('/^__/', $method); });
        $methods = array_flip($methods);
        $helpers = array();
        foreach ($methods as $method => $index) {
            $helpers[$method] = array($class, $method);
        }

        $this->helpers = array_merge($this->helpers, $helpers);

        return $this;
    }

    /**
     * Register all functions in the given namespace as helpers.
     *
     * Example:
     * <code>
     * $collection->registerNamespaceFunctions('Functional');
     * </code>
     *
     * @param string $namespace Without slashes in the end!
     *
     * @return static
     */
    public function registerNamespaceFunctions($namespace)
    {
        // Remove magic methods.
        $functions = get_defined_functions();
        $functions = array_map(
            function ($function) {
                return new \ReflectionFunction($function);
            },
            $functions['user']
        );

        $helpers = array();
        foreach ($functions as $function) {
            if ($function->getNamespaceName() == $namespace) {
                $helpers[$this->camelize($function->getShortName())] = $function->getName();
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
     * $collection->register('trim', 'trim');
     *
     * // Register clojure as helper.
     * $collection->register('splitByCommas', function ($string) { return explode(',', $string); });
     * </code>
     *
     * @param string $name
     * @param callback $callback
     *
     * @return static
     */
    public function register($name, $callback)
    {
        $this->helpers[$name] = $callback;

        return $this;
    }

    /**
     * @return static
     */
    public function clear()
    {
        $this->helpers = array();

        return $this;
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
