<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class FilterOperator extends BinaryOperator
{

    public function operators()
    {
        return '|';
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        //intentionally empty
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        //intentionally empty
    }

    public function createNode($left, $right)
    {
        $node = new FunctionCallNode($right);
        $node->addArgument($left);

        return $node;
    }
}