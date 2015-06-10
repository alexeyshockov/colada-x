<?php

namespace spec\Colada\X;

use PhpSpec\ObjectBehavior;

class FutureValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Colada\X\FutureValue');
    }

    function it_wraps_method_calls(\DateTime $date)
    {
        $date->format('c')->willReturn('2015-06-05T23:21:00+03:00')->shouldBeCalled();

        $this->format('c')->shouldHaveType('\Colada\X\FutureValue');

        $this($date)->shouldReturn('2015-06-05T23:21:00+03:00');
    }

    function it_wraps_array_access(\ArrayObject $collection)
    {
        $collection->offsetGet(1)->willReturn('one')->shouldBeCalled();

        $this[1]->shouldHaveType('\Colada\X\FutureValue');

        $this($collection)->shouldReturn('one');
    }

    function it_wraps_chain_calls()
    {

    }

    function it_transforms_to_closure()
    {
        $this->__toClosure()->shouldHaveType('\Closure');
    }
}
