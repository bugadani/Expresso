<?php

namespace Expresso\Extensions\Bitwise\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class XorOperator extends SimpleBinaryOperator
{
    public function operators()
    {
        return 'b-xor';
    }

    public function executeSimple($left, $right)
    {
        return $left ^ $right;
    }

    public function compiledOperator()
    {
        return '^';
    }
}