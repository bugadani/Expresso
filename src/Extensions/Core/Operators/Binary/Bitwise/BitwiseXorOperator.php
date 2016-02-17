<?php

namespace Expresso\Extensions\Core\Operators\Binary\Bitwise;

use Expresso\Compiler\Operators\BinaryOperator;

class BitwiseXorOperator extends BinaryOperator
{

    protected function evaluateSimple($left, $right)
    {
        return $left ^ $right;
    }

    protected function compiledOperator()
    {
        return '^';
    }
}