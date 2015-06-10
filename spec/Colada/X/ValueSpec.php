<?php

namespace spec\Colada\X;

use Colada\X\ValueHelperCollection;
use PhpSpec\ObjectBehavior;

class ValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Colada\X\Value');
    }

    function it_proxies_field_access()
    {
        $interval = new \DateInterval('P2Y4DT6H8M');

        $this->beConstructedWith($interval);

        $formattedInterval = $this->y;
        $formattedInterval->shouldHaveType('Colada\\X\\Value');
        $formattedInterval->__getValue()->shouldReturn(2);
    }

    function it_proxies_method_access()
    {
        $interval = new \DateInterval('P2Y4DT6H8M');

        $this->beConstructedWith($interval);

        $formattedInterval = $this->format('%d days');
        $formattedInterval->shouldHaveType('Colada\\X\\Value');
        $formattedInterval->__getValue()->shouldReturn('4 days');
    }

    function it_proxies_array_access()
    {

    }

    function it_proxies_magic_invoke_access()
    {

    }

    function it_does_not_use_helper_when_method_is_available(\DateTime $date)
    {
        ValueHelperCollection::getInstance()->clear();
        ValueHelperCollection::getInstance()->register('format', function () { });

        $date->format('c')->willReturn('2015-06-05T23:21:00+03:00')->shouldBeCalled();

        $this->beConstructedWith($date);

        $this->format('c')->__getValue()->shouldReturn('2015-06-05T23:21:00+03:00');
    }

    function it_uses_helper_when_method_is_not_available()
    {
        ValueHelperCollection::getInstance()->clear();
        ValueHelperCollection::getInstance()->register('customFormat', function ($date, $format) { return $date->format($format); });

        // We cannot use mock, because it defines __call() method.
        $date = new \DateTime('2015-06-05T23:21:00+03:00');

        $this->beConstructedWith($date);

        $formattedDate = $this->customFormat('c');
        $formattedDate->shouldHaveType('Colada\\X\\Value');
        $formattedDate->__getValue()->shouldReturn('2015-06-05T23:21:00+03:00');
    }
}
