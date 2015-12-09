<?php

namespace Expresso\Extensions\Strings\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class ConcatenationOperator extends SimpleBinaryOperator
{

    public function operators()
    {
        return '~';
    }

    public function compiledOperator()
    {
        return '.';
    }

    public function executeSimple($left, $right)
    {
        return $left . $right;
    }
}