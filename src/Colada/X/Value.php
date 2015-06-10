<?php

namespace Colada\X;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 *
 * @internal
 */
class Value implements \ArrayAccess
{
    /**
     * @var mixed
     */
    private $value;

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
    public function __getValue()
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
        if (is_array($this->value) || (is_object($this->value) && ($this->value instanceof \ArrayAccess))) {
            return new static($this->value[$key]);
        }

        throw new \BadMethodCallException('ArrayAccess unsupported for current value.');
    }

    /**
     * @param mixed $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        if (is_array($this->value) || (is_object($this->value) && ($this->value instanceof \ArrayAccess))) {
            unset($this->value[$key]);

            return;
        }

        throw new \BadMethodCallException('ArrayAccess unsupported for current value.');
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_array($this->value) || (is_object($this->value) && ($this->value instanceof \ArrayAccess))) {
            return isset($this->value[$key]);
        }

        throw new \BadMethodCallException('ArrayAccess unsupported for current value.');
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_array($this->value) || (is_object($this->value) && ($this->value instanceof \ArrayAccess))) {
            $this->value[$key] = $value;

            return;
        }

        throw new \BadMethodCallException('ArrayAccess unsupported for current value.');
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

        throw new \BadMethodCallException('__get() is unsupported for current value.');
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

        throw new \BadMethodCallException('__set() is unsupported for current value.');
    }

    public function __isset($field)
    {
        if (is_object($this->value)) {
            return isset($this->value->$field);
        }

        throw new \BadMethodCallException('__isset() is unsupported for current value.');
    }

    public function __unset($field)
    {
        if (is_object($this->value)) {
            unset($this->value->$field);

            return;
        }

        throw new \BadMethodCallException('__unset() is unsupported for current value.');
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return static
     */
    public function __call($name, $arguments)
    {
        $helpers = ValueHelperCollection::getInstance();

        $method = null;
        if (is_object($this->value) && is_callable(array($this->value, $name))) {
            $method = array($this->value, $name);
        } elseif (isset($helpers[$name])) {
            $method = $helpers[$name];

            array_unshift($arguments, $this->value);
        } else {
            throw new \BadMethodCallException('Unknown method "'.$name.'".');
        }

        $result = call_user_func_array($method, $arguments);

        return new static($result);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
