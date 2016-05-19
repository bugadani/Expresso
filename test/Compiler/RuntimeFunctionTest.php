<?php

namespace Expresso\Test\Compiler;

use Expresso\Runtime\RuntimeFunction;

class RuntimeFunctionTest extends \PHPUnit_Framework_TestCase
{

    public function testFunctionWithoutArguments()
    {
        $function = function() {
            return 2;
        };
        $wrapped = RuntimeFunction::new($function);
        $this->assertEquals(2, $wrapped());
    }

    public function testFunctionWithOneArgument()
    {
        $function = function($x) {
            return $x;
        };
        $wrapped = RuntimeFunction::new($function);
        $this->assertEquals(2, $wrapped(2));
    }

    public function testFunctionWithMultipleArguments()
    {
        $function = function($x, $y) {
            return $x + $y;
        };
        $wrapped = RuntimeFunction::new($function);
        $this->assertEquals(5, $wrapped(2, 3));
    }

    public function testPartiallyAppliedFunction()
    {
        $function = function($x, $y) {
            return $x + $y;
        };
        $wrapped = RuntimeFunction::new($function);
        $partial  = $wrapped(2);
        $this->assertTrue(is_callable($partial));
        $this->assertEquals(3,$partial(1));
        $this->assertEquals(4,$partial(2));
    }
}