<?php

namespace Colada\X;

use ArrayAccess;
use BadMethodCallException;

/**
 * Immutable value wrapper.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Value implements ArrayAccess, ValueWrapper
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var array
     */
    private $helpers;

    /**
     * @param array $helpers
     *
     * @return \Closure
     */
    public static function getConstructorForValue(array $helpers = array())
    {
        return function ($value) use ($helpers) {
            return new static($value, $helpers);
        };
    }

    /**
     * @param mixed $value
     * @param array $helpers
     */
    public function __construct($value = null, array $helpers = array())
    {
        $this->value = $value;
        $this->helpers = $helpers;
    }

    /**
     * @return mixed
     */
    public function __getWrappedValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $key
     *
     * @return static
     */
    public function offsetGet($key)
    {
        if (is_array_accessible($this->value)) {
            return new static($this->value[$key]);
        }

        throw new BadMethodCallException('ArrayAccess unsupported for current value.');
    }

    /**
     * @param mixed $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        if (is_array_accessible($this->value)) {
            unset($this->value[$key]);

            return;
        }

        throw new BadMethodCallException('ArrayAccess unsupported for current value.');
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_array_accessible($this->value)) {
            return isset($this->value[$key]);
        }

        throw new BadMethodCallException('ArrayAccess unsupported for current value.');
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_array_accessible($this->value)) {
            $this->value[$key] = $value;

            return;
        }

        throw new BadMethodCallException('ArrayAccess unsupported for current value.');
    }

    /**
     * @param string $field
     *
     * @return static
     */
    public function __get($field)
    {
        if (is_object($this->value)) {
            return new static($this->value->$field);
        }

        throw new BadMethodCallException('__get() is unsupported for current value.');
    }

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return void Result is always the assigned value.
     */
    public function __set($field, $value)
    {
        if (is_object($this->value)) {
            $this->value->$field = $value;
        }

        throw new BadMethodCallException('__set() is unsupported for current value.');
    }

    public function __isset($field)
    {
        if (is_object($this->value)) {
            return isset($this->value->$field);
        }

        throw new BadMethodCallException('__isset() is unsupported for current value.');
    }

    public function __unset($field)
    {
        if (is_object($this->value)) {
            unset($this->value->$field);

            return;
        }

        throw new BadMethodCallException('__unset() is unsupported for current value.');
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return static
     */
    public function __call($name, $arguments)
    {
        if (is_object($this->value) && is_callable(array($this->value, $name))) {
            $method = array($this->value, $name);
        } elseif (isset($this->helpers[$name])) {
            // TODO Check callable.
            $method = $this->helpers[$name];

            array_unshift($arguments, $this->value);
        } else {
            throw new BadMethodCallException('Unknown method "' . $name . '".');
        }

        $result = call_user_func_array($method, $arguments);

        return new static($result);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // TODO We need to write something for objects without __toString(), otherwise exception will be thrown.
        return (string) $this->value;
    }
}
