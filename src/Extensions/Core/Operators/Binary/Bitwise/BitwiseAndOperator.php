<?php

namespace Expresso\Extensions\Core\Operators\Binary\Bitwise;

use Expresso\Compiler\Operators\BinaryOperator;

class BitwiseAndOperator extends BinaryOperator
{

    protected function evaluateSimple($left, $right)
    {
        return $left & $right;
    }

    protected function compiledOperator()
    {
        return '&';
    }
}