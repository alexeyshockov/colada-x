<?php

namespace spec\Colada\X;

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

    /**
     * @param \DateTime $date
     */
    function it_records_method_calls($date)
    {
        $date->format('c')->willReturn('2015-06-05T23:21:00+03:00')->shouldBeCalled();

        $this->format('c')->shouldBePlayedAs($date, '2015-06-05T23:21:00+03:00');

        // It's still immutable.
        $this->shouldBeEmpty();
    }

    /**
     * @param \ArrayObject $collection
     */
    function it_records_array_access($collection)
    {
        $collection->offsetGet(1)->willReturn('one')->shouldBeCalled();

        $this[1]->shouldBePlayedAs($collection, 'one');

        // It's still immutable.
        $this->shouldBeEmpty();
    }

    /**
     * @param \DateTime $date
     * @param \DateTimeZone $timeZone
     */
    function it_records_chain_calls($date, $timeZone)
    {
        $date->getTimezone()->willReturn($timeZone)->shouldBeCalled();
        $timeZone->getName()->willReturn('+03:00')->shouldBeCalled();

        $this->getTimezone()->getName()->shouldBePlayedAs($date, '+03:00');

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

        $this->shouldBePlayedAs(' Some value. ', 'Some value.');
    }

    /**
     * @param \Colada\X\ValueWrapper $wrapper
     */
    function it_treats_value_wrappers_specially($wrapper)
    {
        $wrapper->__getWrappedValue()->willReturn('Wrapped value.')->shouldBeCalled();
        // Explicit call is needed, because we will use this mock inside a closure, and PHPSpec will not be able to
        // unwrap this mock for us.
        $wrapper = $wrapper->getWrappedObject();

        // See Value::getConstructorForValue().
        $this->beConstructedWith(function ($value) use ($wrapper) { return $wrapper; });

        $this->shouldBePlayedAs('Some value.', 'Wrapped value.');
    }

    public function getMatchers()
    {
        return [
            'beEmpty' => function ($recorder) {
                $value = 'Some value.';
                if ($value !== $recorder($value)) {
                    throw new FailureException("Recorder isn't empty.");
                }

                return true;
            },
            'bePlayedAs' => function ($recorder, $value, $expectedResult) {
                $result = $recorder($value);

                if ($result !== $expectedResult) {
                    throw new FailureException("Result doesn't match.");
                }

                return true;
            }
        ];
    }
}
