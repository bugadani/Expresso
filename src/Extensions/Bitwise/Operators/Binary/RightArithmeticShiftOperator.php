<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class RightArithmeticShiftOperator extends BinaryOperator
{
    public function operators()
    {
        return '>>';
    }

    public function evaluateSimple($left, $right)
    {
        return $left >> $right;
    }

    public function compiledOperator()
    {
        return '>>';
    }
}