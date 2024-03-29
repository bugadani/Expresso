<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Operators\BinaryOperator;

class SubtractionOperator extends BinaryOperator
{

    protected function compiledOperator()
    {
        return '-';
    }

    protected function evaluateSimple($left, $right)
    {
        return $left - $right;
    }
}