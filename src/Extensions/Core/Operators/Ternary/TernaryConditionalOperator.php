<?php

namespace Expresso\Extensions\Core\Operators\Ternary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\Runtime\ExecutionContext;
use Expresso\Extensions\Core\Nodes\ConditionalNode;

class TernaryConditionalOperator extends TernaryOperator
{

    public function createNode(CompilerConfiguration $config, Node ...$operands) : Node
    {
        list($left, $middle, $right) = $operands;

        return new ConditionalNode($config, $left, $middle, $right);
    }

    public function evaluate(ExecutionContext $context, Node $node)
    {

    }

    public function compile(Compiler $compiler, Node $node)
    {

    }
}
