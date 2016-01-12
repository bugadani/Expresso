<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Operators\BinaryOperator;

class MultiplicationOperator extends BinaryOperator
{

    public function operators()
    {
        return '*';
    }

    protected function evaluateSimple($left, $right)
    {
        return $left * $right;
    }

    protected function compiledOperator()
    {
        return '*';
    }
}