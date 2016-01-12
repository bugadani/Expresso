<?php

namespace Expresso\Extensions\Core\Operators\Binary\Comparison;

use Expresso\Compiler\Operators\BinaryOperator;

class GreaterThanOperator extends BinaryOperator
{

    public function operators()
    {
        return '>';
    }

    protected function evaluateSimple($left, $right)
    {
        return $left > $right;
    }

    protected function compiledOperator()
    {
        return '>';
    }
}