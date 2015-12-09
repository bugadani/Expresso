<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class LeftArithmeticShiftOperator extends BinaryOperator
{
    public function operators()
    {
        return '<<';
    }

    public function evaluateSimple($left, $right)
    {
        return $left << $right;
    }

    public function compiledOperator()
    {
        return '<<';
    }
}