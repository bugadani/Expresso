<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

abstract class TernaryOperator extends Operator
{
    abstract public function evaluate(EvaluationContext $context, Node $left, Node $middle, Node $right);

    abstract public function compile(Compiler $compiler, Node $left, Node $middle, Node $right);

    public function createNode(CompilerConfiguration $config, $left, $middle, $right)
    {
        return new TernaryOperatorNode($this, $left, $middle, $right);
    }
}