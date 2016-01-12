<?php

namespace Expresso\Extensions\Core\Operators\Binary\Bitwise;

use Expresso\Compiler\Operators\BinaryOperator;

class BitwiseOrOperator extends BinaryOperator
{
    public function operators()
    {
        return 'b-or';
    }

    protected function evaluateSimple($left, $right)
    {
        return $left | $right;
    }

    protected function compiledOperator()
    {
        return '|';
    }
}