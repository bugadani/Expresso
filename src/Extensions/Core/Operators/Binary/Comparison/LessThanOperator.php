<?php

namespace Expresso\Extensions\Core\Operators\Binary\Comparison;

use Expresso\Compiler\Operators\BinaryOperator;

class LessThanOperator extends BinaryOperator
{

    protected function evaluateSimple($left, $right)
    {
        return $left < $right;
    }

    protected function compiledOperator()
    {
        return '<';
    }
}