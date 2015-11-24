<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class AdditionOperator extends SimpleBinaryOperator
{

    public function operators()
    {
        return '+';
    }

    public function executeSimple($left, $right)
    {
        return $left + $right;
    }

    public function compiledOperator()
    {
        return '+';
    }
}