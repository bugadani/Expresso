<?php

namespace Expresso\Compiler\Operators;

class SubtractionOperator extends BinaryOperator
{

    public function operators()
    {
        return '-';
    }

    public function execute($left, $right)
    {
        return $left - $right;
    }

    public function compile($left, $right)
    {
        // TODO: Implement compile() method.
    }
}