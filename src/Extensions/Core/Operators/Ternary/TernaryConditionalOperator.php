<?php

namespace Expresso\Extensions\Core\Operators\Ternary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Nodes\ConditionalNode;

class TernaryConditionalOperator extends TernaryOperator
{

    public function createNode(CompilerConfiguration $config, Node ...$operands) : Node
    {
        list($left, $middle, $right) = $operands;

        return new ConditionalNode($config, $left, $middle, $right);
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {

    }

    public function compile(Compiler $compiler, Node $node)
    {

    }
}
