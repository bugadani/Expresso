<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class LeftArithmeticShiftOperator extends SimpleBinaryOperator
{
    public function operators()
    {
        return '<<';
    }

    public function executeSimple($left, $right)
    {
        return $left << $right;
    }

    public function compiledOperator()
    {
        return '<<';
    }
}