<?php

namespace Colada\X;

use Closure;
use ArrayAccess;

/**
 * Immutable functor, that collects behaviour and resolves it to concrete value
 *
 * Will NOT be implemented: __isset(). Useless, because call to isset() or empty() is external and can not be wrapped.
 * Will NOT be implemented: __unset(). Useless, because call to unset() is external and can not be wrapped.
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
        return $this->__call(__FUNCTION__, array($key));
    }

    public function offsetUnset($key)
    {
        return $this->__call(__FUNCTION__, array($key));
    }

    public function offsetExists($key)
    {
        return $this->__call(__FUNCTION__, array($key));
    }

    public function offsetSet($key, $value)
    {
        return $this->__call(__FUNCTION__, array($key, $value));
    }

    public function __set($property, $value)
    {
        return new static(function ($value) use ($property, $value) {
            return $this->mapper->__invoke($value)->$property = $value;
        });
    }

    public function __get($property)
    {
        return new static(function ($value) use ($property) {
            return $this->mapper->__invoke($value)->$property;
        });
    }

    public function __call($method, $arguments)
    {
        return new static(function ($value) use ($method, $arguments) {
            return call_user_func_array([$this->mapper->__invoke($value), $method], $arguments);
        });
    }

    /**
     * Play recorded actions on concrete value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function __invoke($value)
    {
        return $this->__asClosure()->__invoke($value);
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
        return function ($value) {
            // In the end return original value.
            $result = $this->mapper->__invoke($value);
            if (is_object($result) && ($result instanceof ValueWrapper)) {
                $result = $result->__getWrappedValue();
            }

            return $result;
        };
    }
}
