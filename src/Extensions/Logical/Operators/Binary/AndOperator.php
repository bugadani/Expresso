<?php

namespace Expresso\Extensions\Logical\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class AndOperator extends SimpleBinaryOperator
{

    public function operators()
    {
        return '&&';
    }

    public function executeSimple($left, $right)
    {
        return $left && $right;
    }

    public function compiledOperator()
    {
        return '&&';
    }
}