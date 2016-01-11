<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\EvaluationContext;

class FunctionCallOperator extends BinaryOperator
{
    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        if ($left instanceof TernaryOperatorNode) {

            list($opL, $opM, $opR) = $left->getChildren();

            $right = new FunctionCallNode($opR, $right);
            $newNode = new TernaryOperatorNode(
                $left->getOperator(),
                $opL,
                $opM,
                $right
            );
        } else {
            $newNode = new FunctionCallNode($left, $right);
        }

        return $newNode->setInline($left->isInline());
    }

    public function operators()
    {
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {
    }

    public function compile(Compiler $compiler, Node $node)
    {
    }
}