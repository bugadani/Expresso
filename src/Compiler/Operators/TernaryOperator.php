<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

abstract class TernaryOperator extends Operator
{
    abstract public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $middle, NodeInterface $right);

    abstract public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $middle, NodeInterface $right);

    public function createNode($left, $middle, $right)
    {
        return new TernaryOperatorNode($this, $left, $middle, $right);
    }
}