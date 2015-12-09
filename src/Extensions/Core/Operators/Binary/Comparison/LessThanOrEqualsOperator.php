<?php

namespace Expresso\Extensions\Core\Operators\Binary\Comparison;

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