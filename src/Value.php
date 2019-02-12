<?php

namespace Colada\X;

use ArrayAccess;
use BadMethodCallException;

/**
 * Immutable value wrapper
 *
 * @author Alexey Shokov <alexey@shockov.com>
 */
class Value implements ArrayAccess, ValueWrapper
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @return \Closure
     */
    public static function getConstructorForValue()
    {
        return function ($value) {
            return new static($value);
        };
    }

    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
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
        if (is_object($this->value) && is_callable([$this->value, $name])) {
            $method = [$this->value, $name];
        } else {
            throw new BadMethodCallException('Unknown method "' . $name . '"');
        }

        $result = call_user_func_array($method, $arguments);

        return new static($result);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // For objects without __toString() an exception will be thrown. This is OK, because the same will happen if
        // __toString() is called directly on them.
        return (string) $this->value;
    }
}
