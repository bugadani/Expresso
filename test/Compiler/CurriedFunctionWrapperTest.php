<?php

namespace Expresso\Test\Compiler;

use Expresso\Compiler\RuntimeFunction;

class CurriedFunctionWrapperTest extends \PHPUnit_Framework_TestCase
{

    public function testFunctionWithoutArguments()
    {
        $function = function() {
            return 2;
        };
        $wrapped = new RuntimeFunction($function);
        $this->assertEquals(2, $wrapped());
    }

    public function testFunctionWithOneArgument()
    {
        $function = function($x) {
            return $x;
        };
        $wrapped = new RuntimeFunction($function);
        $this->assertEquals(2, $wrapped(2));
    }

    public function testFunctionWithMultipleArguments()
    {
        $function = function($x, $y) {
            return $x + $y;
        };
        $wrapped = new RuntimeFunction($function);
        $this->assertEquals(5, $wrapped(2, 3));
    }

    public function testCurriedFunction()
    {
        $function = function($x, $y) {
            return $x + $y;
        };
        $wrapped = new RuntimeFunction($function);
        $curried  = $wrapped(2);
        $this->assertTrue(is_callable($curried));
        $this->assertEquals(3,$curried(1));
        $this->assertEquals(4,$curried(2));
    }
}