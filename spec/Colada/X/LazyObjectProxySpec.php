<?php

namespace spec\Colada\X;

use ArrayObject;
use DateTime;
use DateTimeZone;
use Colada\X\ValueWrapper;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\FailureException;

/**
 * @todo it unwraps value on resolve
 *
 * @mixin \Colada\X\LazyObjectProxy
 */
class LazyObjectProxySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Colada\X\LazyObjectProxy');
    }

    function it_records_method_calls(DateTime $date)
    {
        $date->format('c')->willReturn('2015-06-05T23:21:00+03:00')->shouldBeCalled();

        $this->format('c')->shouldPlay($date, '2015-06-05T23:21:00+03:00');

        // It's still immutable.
        $this->shouldBeEmpty($date, $date);
    }

    function it_records_array_access(ArrayObject $collection)
    {
        $collection->offsetGet(1)->willReturn('one')->shouldBeCalled();

        $this[1]->shouldPlay($collection, 'one');

        // It's still immutable.
        $this->shouldBeEmpty();
    }

    function it_records_chain_calls(DateTime $date, DateTimeZone $timeZone)
    {
        $date->getTimezone()->willReturn($timeZone)->shouldBeCalled();
        $timeZone->getName()->willReturn('+03:00')->shouldBeCalled();

        $this->getTimezone()->getName()->shouldPlay($date, '+03:00');

        // It's still immutable.
        $this->shouldBeEmpty();
    }

    function it_is_available_as_closure()
    {
        $this->__asClosure()->shouldHaveType('\Closure');
    }

    function it_supports_custom_initial_action()
    {
        $this->beConstructedWith('trim');

        $this->shouldPlay(' Some value. ', 'Some value.');
    }

    function it_treats_value_wrappers_specially(ValueWrapper $wrapper)
    {
        $wrapper->__getWrappedValue()->willReturn('Wrapped value.')->shouldBeCalled();
        // Explicit call is needed, because we will use this mock inside a closure, and PHPSpec will not be able to
        // unwrap this mock for us.
        $wrapper = $wrapper->getWrappedObject();

        // See Value::getConstructorForValue().
        $this->beConstructedWith(function ($value) use ($wrapper) { return $wrapper; });

        $this->shouldPlay('Some value.', 'Wrapped value.');
    }

    public function getMatchers()
    {
        return array(
            'beEmpty' => function ($recorder) {
                $value = 'Some value.';
                if ($value !== $recorder($value)) {
                    throw new FailureException("Recorder isn't empty.");
                }

                return true;
            },
            'play' => function ($recorder, $value, $expectedResult) {
                $result = $recorder($value);

                if ($result !== $expectedResult) {
                    throw new FailureException("Result doesn't match.");
                }

                return true;
            }
        );
    }
}
