<?php

namespace Expresso\Extensions\Core\Operators\Binary\Strings;

use Expresso\Compiler\Operators\BinaryOperator;

class ConcatenationOperator extends BinaryOperator
{

    protected function compiledOperator()
    {
        return '.';
    }

    protected function evaluateSimple($left, $right)
    {
        return $left . $right;
    }
}