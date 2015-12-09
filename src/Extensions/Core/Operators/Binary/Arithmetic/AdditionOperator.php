<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Operators\BinaryOperator;

class AdditionOperator extends BinaryOperator
{

    public function operators()
    {
        return '+';
    }

    public function evaluateSimple($left, $right)
    {
        return $left + $right;
    }

    public function compiledOperator()
    {
        return '+';
    }
}