<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;


abstract class BinaryOperator extends Operator
{
    abstract public function execute(EvaluationContext $context, Node $left, Node $right);

    abstract public function compile(Compiler $compiler, Node $left, Node $right);

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        return new BinaryOperatorNode($this, $left, $right);
    }
}