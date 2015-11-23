<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operator;

abstract class UnaryOperator extends Operator
{
    abstract public function execute($operand);

    abstract public function compile(Compiler $compiler, NodeInterface $operand);

    public function createNode($operand)
    {
        return new UnaryOperatorNode($this, $operand);
    }
}