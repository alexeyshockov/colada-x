<?php

namespace Colada\X;

use Closure;
use ArrayAccess;

/**
 * Immutable functor, that collects behaviour and resolves it to concrete value
 *
 * Will NOT be implemented: __isset(). Useless, because call to isset() or empty() is external and can not be wrapped.
 * Will NOT be implemented: __unset(). Useless, because call to unset() is external and can not be wrapped.
 * Will NOT be implemented: __set(). Useless, because assigning a value is an external action and can not be wrapped.
 *
 * Will NOT be implemented: \Traversable support. Useless, because work with traversable value is external and can not be wrapped.
 * Will NOT be implemented: \Countable support. Useless, because call to count() is external and can not be wrapped.
 *
 * Will NOT be implemented: __toString(). This method can't be collected, it should always return a string.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class LazyObjectProxy implements ArrayAccess
{
    /**
     * @var Closure
     */
    private $mapper;

    /**
     * @param callable $constructor Not required. \Colada\X\id function will be used by default.
     */
    public function __construct(callable $constructor = null)
    {
        $constructor = $constructor ?: 'Colada\\X\\id';

        $this->mapper = as_closure($constructor);
    }

    public function offsetGet($key)
    {
        return $this->__call(__FUNCTION__, [$key]);
    }

    /**
     * Call to this method should be available, because it can be called explicitly
     *
     * @param mixed $key
     *
     * @return static
     */
    public function offsetUnset($key)
    {
        return $this->__call(__FUNCTION__, [$key]);
    }

    public function offsetExists($key)
    {
        return $this->__call(__FUNCTION__, [$key]);
    }

    /**
     * Call to this method should be available, because it can be called explicitly
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return static
     */
    public function offsetSet($key, $value)
    {
        return $this->__call(__FUNCTION__, [$key, $value]);
    }

    public function __get($property)
    {
        return new static(function (...$arguments) use ($property) {
            return $this->mapper->__invoke(...$arguments)->$property;
        });
    }

    public function __call($method, $methodArguments)
    {
        return new static(function (...$arguments) use ($method, $methodArguments) {
            return call_user_func_array([$this->mapper->__invoke(...$arguments), $method], $methodArguments);
        });
    }

    /**
     * Play recorded actions on concrete value
     *
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    public function __invoke(...$arguments)
    {
        return $this->__asClosure()->__invoke(...$arguments);
    }

    /**
     * Recorder as \Closure object
     *
     * Useful for working in places, that don't allow anything except \Closure instances. Like Doctrine's ArrayCollection.
     *
     * @return Closure
     */
    public function __asClosure()
    {
        return function (...$arguments) {
            // In the end return original value.
            $result = $this->mapper->__invoke(...$arguments);
            if (is_object($result) && ($result instanceof ValueWrapper)) {
                $result = $result->__getWrappedValue();
            }

            return $result;
        };
    }
}
