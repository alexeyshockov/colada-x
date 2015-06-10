<?php

namespace Colada;

use Colada\X\FutureValue;
use Colada\X\Value;

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
 * @return \Colada\X\FutureValue
 */
function x()
{
    return new FutureValue();
}

/**
 * May be useful with helpers (@see \Colada\X\ValueHelperCollection).
 *
 * Example (for PHP 5.6):
 * <code>
 * use Colada\X\ValueHelperCollection;
 *
 * use function Colada\constant as c;
 *
 * ValueHelperCollection::getInstance()->registerClassMethods('Stringy\\StaticStringy');
 *
 * $string = c('the quick brown fox jumps over the lazy dog')->upperCaseFirst()->ensureRight('.')->__getValue();
 * </code>
 *
 * @param mixed $value
 *
 * @return \Colada\X\Value
 */
function constant($value = null)
{
    return new Value($value);
}
