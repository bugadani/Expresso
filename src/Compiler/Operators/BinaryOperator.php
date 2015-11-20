<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operator;

abstract class BinaryOperator extends Operator
{
    abstract public function execute($left, $right);

    abstract public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right);

    public function createNode($left, $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }
}