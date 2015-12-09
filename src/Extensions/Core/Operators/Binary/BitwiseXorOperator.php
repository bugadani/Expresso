<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class BitwiseXorOperator extends BinaryOperator
{
    public function operators()
    {
        return 'b-xor';
    }

    public function evaluateSimple($left, $right)
    {
        return $left ^ $right;
    }

    public function compiledOperator()
    {
        return '^';
    }
}