<?php

namespace Expresso\Extensions\Strings\Operators\Binary;

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