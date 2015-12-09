<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class AndOperator extends BinaryOperator
{
    public function operators()
    {
        return 'b-and';
    }

    public function evaluateSimple($left, $right)
    {
        return $left & $right;
    }

    public function compiledOperator()
    {
        return '&';
    }
}