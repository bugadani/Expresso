<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Operators\SimpleBinaryOperator;

class NotEqualsOperator extends SimpleBinaryOperator
{

    public function operators()
    {
        return '!=';
    }

    public function executeSimple($left, $right)
    {
        return $left != $right;
    }

    public function compiledOperator()
    {
        return '!=';
    }
}