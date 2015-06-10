<?php

namespace spec\Colada\X;

use PhpSpec\ObjectBehavior;

/**
 * @mixin \Colada\X\ValueHelperCollection
 */
class ValueHelperCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Colada\X\ValueHelperCollection');
    }

    function it_can_register_single_callbacks()
    {

    }

    function it_can_register_functions_from_namespace()
    {

    }

    function it_can_register_class_static_methods()
    {

    }
}
