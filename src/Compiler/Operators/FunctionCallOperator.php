<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\ArgumentListNode;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\EvaluationContext;

class FunctionCallOperator extends BinaryOperator
{
    public function createNode(CompilerConfiguration $config, Node $functionName, Node $arguments)
    {
        if (!$arguments instanceof ArgumentListNode) {
            throw  new ParseException('$arguments must be an instance of ArgumentListNode');
        }
        if ($functionName instanceof TernaryOperatorNode) {

            list($left, $middle, $right) = $functionName->getChildren();

            if (!$middle instanceof DataNode || $middle->getValue() !== null) {
                $middle = new FunctionCallNode($middle, $arguments);
            }
            $arguments = new FunctionCallNode($right, $arguments);

            $newNode = new TernaryOperatorNode(
                $functionName->getOperator(),
                $left,
                $middle,
                $arguments
            );
        } else {
            $newNode = new FunctionCallNode($functionName, $arguments);
        }

        return $newNode->setInline($functionName->isInline());
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