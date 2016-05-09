<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\ArgumentListNode;
use Expresso\Extensions\Core\Nodes\CallableNode;
use Expresso\Extensions\Core\Nodes\FunctionCallNode;
use Expresso\Extensions\Core\Nodes\FunctionNameNode;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\Compiler\Operators\BinaryOperator;

class FilterOperator extends BinaryOperator
{

    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($left, $right) = $operands;
        $arguments = new ArgumentListNode();
        $arguments->add($left);

        if ($right instanceof FunctionCallNode) {
            //arg|funcName(args)
            list($right, $args) = $right->getChildren();

            if (!$right instanceof FunctionNameNode) {
                throw new ParseException("The right hand operand of filter node must be a function");
            }

            /** @var ArgumentListNode $args */
            foreach ($args->getChildren() as $arg) {
                $arguments->add($arg);
            }
        } else if (!$right instanceof IdentifierNode && !$right instanceof CallableNode) {
            //arg|funcName
            throw new ParseException("The right hand operand of filter node must be a function");
        }

        return new FunctionCallNode($right, $arguments);
    }
}