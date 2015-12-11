<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Operators\BinaryOperator;

class SubtractionOperator extends BinaryOperator
{

    public function operators()
    {
        return '-';
    }

    public function compiledOperator()
    {
        return '-';
    }

    public function evaluateSimple($left, $right)
    {
        return $left - $right;
    }
}