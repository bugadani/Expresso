<?php

namespace Expresso\Compiler\Operators\Binary;

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