<?php

namespace Colada
{
    use Colada\X\LazyObjectProxy;
    use InvalidArgumentException;

    if (!function_exists('\\Colada\\x')) {
        /**
         * Represents future value (useful for processing collection elements with {@see \Colada\Collection::mapBy()}, for example).
         *
         * Example:
         * <code>
         * $users->acceptBy(\Colada\x()->isActive());
         * </code>
         *
         * And example for PHP 5.6:
         * <code>
         * use function Colada\x;
         *
         * $users->acceptBy(x()->isActive());
         * </code>
         *
         * vs.
         *
         * <code>
         * $users->acceptBy(
         *     function($user) { return $user->isActive(); }
         * );
         * </code>
         *
         * @api
         *
         * @param null|string $t Return class type hint (for PHPStorm, see .phpstorm.meta.php in the package's root)
         *
         * @return callable
         */
        function x($t = null)
        {
            return new LazyObjectProxy();
        }
    }

    if (!function_exists('\\Colada\\lazy')) {
        /**
         * Wraps object to proxy, that collects all actions and plays them only when asked.
         *
         * Example:
         * <code>
         * use function Colada\lazy;
         *
         * $value = lazy(new \ArrayObject([1, 2, 3]))->count();
         *
         * echo $value(); // Will print "3".
         * </code>
         *
         * @api
         *
         * @param mixed $object
         *
         * @return callable
         */
        function lazy($object)
        {
            if (!is_object($object)) {
                throw new InvalidArgumentException('Only objects can be wrapped.');
            }

            return new LazyObjectProxy(function () use ($object) {
                return $object;
            });
        }
    }
}

namespace Colada\X
{
    use ArrayAccess;
    use Closure;
    use ReflectionFunction;
    use ReflectionMethod;
    use ReflectionObject;

    if (!function_exists('\\Colada\\X\\id')) {
        /**
         * @param $value
         *
         * @return mixed
         */
        function id($value)
        {
            return $value;
        }
    }

    if (!function_exists('\\Colada\\X\\is_array_accessible')) {
        /**
         * @param mixed $value
         *
         * @return bool
         */
        function is_array_accessible($value)
        {
            return is_array($value) || (is_object($value) && ($value instanceof ArrayAccess));
        }
    }

    if (!function_exists('\\Colada\\X\\as_closure')) {
        /**
         * Create \Closure view of given callable
         *
         * Closure style calling can speed up callback (see http://php.net/manual/en/reflectionfunction.getclosure.php
         * for details).
         *
         * @see https://wiki.php.net/rfc/closurefromcallable
         *
         * @param callable $callback
         *
         * @return Closure
         */
        function as_closure(callable $callback)
        {
            if (method_exists('Closure','fromCallable')) {
                // Available as of PHP 7.1
                $closure = Closure::fromCallable($callback);
            } else {
                if (is_object($callback) && ($callback instanceof Closure)) {
                    $closure = $callback;
                } elseif (is_object($callback)) {
                    // Object with __invoke().
                    $closure = (new ReflectionObject($callback))->getMethod('__invoke')->getClosure($callback);
                } elseif (is_array($callback) && is_object($callback[0])) {
                    // Object method.
                    $closure = (new ReflectionMethod($callback[0], $callback[1]))->getClosure($callback[0]);
                } elseif (is_array($callback)) {
                    // Static method.
                    $closure = (new ReflectionMethod($callback[0], $callback[1]))->getClosure();
                } else {
                    // Function name as a string.
                    $closure = (new ReflectionFunction($callback))->getClosure();
                }
            }

            return $closure;
        }
    }
}
