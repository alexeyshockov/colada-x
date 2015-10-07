<?php

namespace Colada\X\Helpers;

use Stringy\StaticStringy;

/**
 * For StringyHelper 2.1+. In 2.0 they completely removed StaticStringy, and in 1.0+
 *
 * @internal
 */
class StringyHelper extends StaticStringy
{
    public static function getClass()
    {
        return '\\Stringy\\StaticStringy';
    }

    public static function getMethods()
    {
        return array_keys(static::$methodArgs);
    }
}
