<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class BitwiseOrOperator extends BinaryOperator
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