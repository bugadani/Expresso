<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;

use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

abstract class UnaryOperator extends Operator
{
    abstract public function execute(EvaluationContext $context, Node $operand);

    abstract public function compile(Compiler $compiler, Node $operand);

    public function createNode(CompilerConfiguration $config, $operand)
    {
        return new UnaryOperatorNode($this, $operand);
    }
}