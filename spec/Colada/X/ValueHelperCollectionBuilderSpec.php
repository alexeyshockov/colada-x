<?php

namespace spec\Colada\X;

use PhpSpec\ObjectBehavior;

/**
 * @mixin \Colada\X\ValueHelperCollectionBuilder
 */
class ValueHelperCollectionBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Colada\X\ValueHelperCollectionBuilder');
    }

    function it_registers_closures()
    {
        $processor = function ($x) { return $x; };

        $this->register($processor, 'process')->shouldReturn($this);

        $this->build()->shouldHaveKeyWithValue('process', $processor);
    }

    function it_registers_global_functions()
    {
        $this->register('trim')->shouldReturn($this);

        $this->build()->shouldHaveKeyWithValue('trim', 'trim');
    }

    function it_registers_namespaced_functions()
    {
        $this->register('\\Functional\\select')->shouldReturn($this);

        $this->build()->shouldHaveKeyWithValue('select', '\\Functional\\select');
    }

    function it_registers_functions_from_namespace()
    {
        $this->registerNamespaceFunctions('\\Functional')->shouldReturn($this);

        $this->build()->shouldHaveKeyWithValue('reject', '\\Functional\\reject');
        // Camel case.
        $this->build()->shouldHaveKeyWithValue('dropFirst', '\\Functional\\drop_first');
    }

    function it_registers_stringy_methods()
    {
        $this->registerStringyHelper()->shouldReturn($this);

        $this->build()->shouldHaveKeyWithValue('startsWith', array('\\Stringy\\StaticStringy', 'startsWith'));
    }

    function it_registers_class_static_methods()
    {
        $this->registerClassMethods('\\Colada\\X\\Helpers\\CarbonHelper')->shouldReturn($this);

        $this->build()->shouldHaveKeyWithValue('toCarbon', array('\\Colada\\X\\Helpers\\CarbonHelper', 'toCarbon'));
    }
}
