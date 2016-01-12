<?php

namespace Expresso\Extensions\Core\Operators\Binary\Bitwise;

use Expresso\Compiler\Operators\BinaryOperator;

class BitwiseXorOperator extends BinaryOperator
{
    public function operators()
    {
        return 'b-xor';
    }

    protected function evaluateSimple($left, $right)
    {
        return $left ^ $right;
    }

    protected function compiledOperator()
    {
        return '^';
    }
}