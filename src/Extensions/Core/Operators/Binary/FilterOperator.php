<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\ArgumentListNode;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\FunctionNameNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Operators\BinaryOperator;

class FilterOperator extends BinaryOperator
{

    public function operators()
    {
        return '|';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $arguments = new ArgumentListNode();
        $arguments->addChild($left);

        if ($right instanceof FunctionCallNode) {
            //arg|funcName(args)
            list($right, $args) = $right->getChildren();

            if (!$right instanceof FunctionNameNode) {
                throw new ParseException("The right hand operand of filter node must be a function");
            }

            /** @var ArgumentListNode $args */
            foreach ($args->getChildren() as $arg) {
                $arguments->addChild($arg);
            }
        } else if (!$right instanceof IdentifierNode) {
            //arg|funcName
            throw new ParseException("The right hand operand of filter node must be a function");
        }

        return new FunctionCallNode($right, $arguments);
    }
}