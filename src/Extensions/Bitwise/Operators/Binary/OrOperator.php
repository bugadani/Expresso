<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class OrOperator extends SimpleBinaryOperator
{
    public function operators()
    {
        return 'b-or';
    }

    public function executeSimple($left, $right)
    {
        return $left | $right;
    }

    public function compiledOperator()
    {
        return '|';
    }
}