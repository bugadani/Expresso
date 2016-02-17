<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Nodes\ArgumentListNode;
use Expresso\Extensions\Core\Nodes\DataNode;
use Expresso\Extensions\Core\Nodes\FunctionCallNode;
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

            return new TernaryOperatorNode(
                $functionName->getOperator(),
                $left,
                $middle,
                $arguments
            );
        } else {
            return new FunctionCallNode($functionName, $arguments);
        }
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