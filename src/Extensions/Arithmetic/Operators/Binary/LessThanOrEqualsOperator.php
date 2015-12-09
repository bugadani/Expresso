<?php

namespace Expresso\Extensions\Arithmetic\Operators\Binary;

use Expresso\Compiler\Operators\BinaryOperator;

class LessThanOrEqualsOperator extends BinaryOperator
{

    public function operators()
    {
        return '<=';
    }

    public function evaluateSimple($left, $right)
    {
        return $left <= $right;
    }

    public function compiledOperator()
    {
        return '<=';
    }
}