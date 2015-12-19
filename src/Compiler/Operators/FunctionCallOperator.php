<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operator;
use Expresso\EvaluationContext;

class FunctionCallOperator extends Operator
{
    public function createNode($left)
    {
        if (!$left instanceof FunctionCallNode) {
            if ($left instanceof TernaryOperatorNode) {
                $node  = $left->getRight();
                $right = new FunctionCallNode($node);
                $left = new TernaryOperatorNode(
                    $left->getOperator(),
                    $left->getLeft(),
                    $left->getMiddle(),
                    $right
                );
            } else {
                $left = new FunctionCallNode($left);
            }
        }

        return $left;
    }

    public function operators()
    {

    }

    public function evaluate(EvaluationContext $context, Node $node)
    {

    }
}