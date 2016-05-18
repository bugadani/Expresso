<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Runtime\ExecutionContext;
use Expresso\Extensions\Core\Nodes\ArgumentListNode;
use Expresso\Extensions\Core\Nodes\FunctionCallNode;
use Expresso\Extensions\Core\Nodes\FunctionNameNode;
use Expresso\Extensions\Core\Nodes\IdentifierNode;

class FunctionCallOperator extends BinaryOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($functionName, $arguments) = $operands;
        if (!$arguments instanceof ArgumentListNode) {
            throw  new ParseException('$arguments must be an instance of ArgumentListNode');
        }
        if ($functionName instanceof IdentifierNode) {
            $functionName = new FunctionNameNode($functionName->getName());
        }

        return new FunctionCallNode($functionName, $arguments);
    }

    public function evaluate(ExecutionContext $context, Node $node)
    {
    }

    public function compile(Compiler $compiler, Node $node)
    {
    }
}