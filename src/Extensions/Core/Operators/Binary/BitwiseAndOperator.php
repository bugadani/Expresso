<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class BitwiseAndOperator extends BinaryOperator
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