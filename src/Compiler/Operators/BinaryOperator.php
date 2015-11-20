<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operator;

abstract class BinaryOperator extends Operator
{
    abstract public function execute($left, $right);

    abstract public function compile($left, $right);

    public function createNode($left, $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }
}