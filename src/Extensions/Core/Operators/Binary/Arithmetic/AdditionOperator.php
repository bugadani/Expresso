<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Operators\BinaryOperator;

class AdditionOperator extends BinaryOperator
{
    protected function evaluateSimple($left, $right)
    {
        return $left + $right;
    }

    protected function compiledOperator()
    {
        return '+';
    }
}