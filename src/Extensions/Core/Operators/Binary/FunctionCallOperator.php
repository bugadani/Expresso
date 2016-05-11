<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Nodes\ArgumentListNode;
use Expresso\Extensions\Core\Nodes\ConditionalNode;
use Expresso\Extensions\Core\Nodes\DataNode;
use Expresso\Extensions\Core\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\EvaluationContext;

class FunctionCallOperator extends BinaryOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($functionName, $arguments) = $operands;
        if (!$arguments instanceof ArgumentListNode) {
            throw  new ParseException('$arguments must be an instance of ArgumentListNode');
        }
        return new FunctionCallNode($functionName, $arguments);
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {
    }

    public function compile(Compiler $compiler, Node $node)
    {
    }
}