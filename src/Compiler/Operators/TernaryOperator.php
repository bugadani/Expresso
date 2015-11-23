<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;

abstract class TernaryOperator extends Operator
{
    abstract public function execute($left, $middle, $right);

    abstract public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $middle, NodeInterface $right);

    public function createNode($left, $middle, $right)
    {
        return new TernaryOperatorNode($this, $left, $middle, $right);
    }
}