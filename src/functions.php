<?php

namespace Colada
{
    use Colada\X\LazyObjectProxy;

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
         * @return \Colada\X\LazyObjectProxy
         */
        function x()
        {
            return new LazyObjectProxy();
        }
    }
}

namespace Colada\X
{
    use Closure;
    use ReflectionFunction;
    use ReflectionMethod;
    use ReflectionObject;

    if (!function_exists('\\Colada\\X\\id')) {
        /**
         * @internal
         *
         * @param $value
         *
         * @return mixed
         */
        function id($value)
        {
            return $value;
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
         * @internal
         *
         * @param callable $callback
         *
         * @return Closure
         */
        function as_closure(callable $callback)
        {
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

            return $closure;
        }
    }
}
