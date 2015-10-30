<?php

namespace spec\Colada\X;

use ArrayObject;
use DateInterval;
use DateTime;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\FailureException;

/**
 * @mixin \Colada\X\Value
 */
class ValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Colada\X\Value');
    }

    function it_proxies_field_access()
    {
        $interval = new DateInterval('P2Y4DT6H8M');

        $this->beConstructedWith($interval);

        $formattedInterval = $this->y;
        $formattedInterval->shouldWrapValue(2);
    }

    function it_proxies_array_access()
    {
        $array = new ArrayObject([1, 2, 3]);

        $this->beConstructedWith($array);

        $this[1]->shouldWrapValue(2);
    }

    /**
     * @param \DateTime $date
     */
    function it_uses_method_if_available($date)
    {
        $date->format('c')->willReturn('2015-06-05T23:21:00+03:00')->shouldBeCalled();

        $this->beConstructedWith($date);

        $this->format('c')->shouldWrapValue('2015-06-05T23:21:00+03:00');
    }

    function it_uses_helper_if_method_is_not_available()
    {
        $helpers = [
            'customFormat' => function ($date, $format) { return $date->format($format); }
        ];
        // We cannot use mock, because it defines __call() method.
        $date = new DateTime('2015-06-05T23:21:00+03:00');

        $this->beConstructedWith($date, $helpers);

        $this->customFormat('c')->shouldWrapValue('2015-06-05T23:21:00+03:00');
    }

    public function getMatchers()
    {
        return [
            'wrapValue' => function ($actual, $value) {
                if ($actual->__getWrappedValue() !== $value) {
                    throw new FailureException("Value doesn't match.");
                }

                return true;
            }
        ];
    }
}
