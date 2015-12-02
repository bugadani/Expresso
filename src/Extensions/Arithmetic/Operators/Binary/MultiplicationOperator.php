<?php

namespace Expresso\Extensions\Arithmetic\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class MultiplicationOperator extends SimpleBinaryOperator
{

    public function operators()
    {
        return '*';
    }

    public function executeSimple($left, $right)
    {
        return $left * $right;
    }

    public function compiledOperator()
    {
        return '*';
    }
}