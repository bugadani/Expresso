<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class XorOperator extends BinaryOperator
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