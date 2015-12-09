<?php

namespace Expresso\Extensions\Core\Operators\Binary\Strings;

use Expresso\Compiler\Operators\BinaryOperator;

class ConcatenationOperator extends BinaryOperator
{

    public function operators()
    {
        return '~';
    }

    public function compiledOperator()
    {
        return '.';
    }

    public function evaluateSimple($left, $right)
    {
        return $left . $right;
    }
}