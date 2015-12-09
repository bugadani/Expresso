<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class OrOperator extends BinaryOperator
{
    public function operators()
    {
        return 'b-or';
    }

    public function evaluateSimple($left, $right)
    {
        return $left | $right;
    }

    public function compiledOperator()
    {
        return '|';
    }
}