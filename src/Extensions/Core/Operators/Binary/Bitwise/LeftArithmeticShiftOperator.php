<?php

namespace Expresso\Extensions\Core\Operators\Binary\Bitwise;

use Expresso\Compiler\Operators\BinaryOperator;

class LeftArithmeticShiftOperator extends BinaryOperator
{
    public function operators()
    {
        return '<<';
    }

    protected function evaluateSimple($left, $right)
    {
        return $left << $right;
    }

    protected function compiledOperator()
    {
        return '<<';
    }
}