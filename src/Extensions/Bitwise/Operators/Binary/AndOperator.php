<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class AndOperator extends SimpleBinaryOperator
{
    public function operators()
    {
        return 'b-and';
    }

    public function executeSimple($left, $right)
    {
        return $left & $right;
    }

    public function compiledOperator()
    {
        return '&';
    }
}