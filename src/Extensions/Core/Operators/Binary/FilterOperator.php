<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;

use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\ArgumentListNode;

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

            /** @var ArgumentListNode $args */
            foreach ($args->getChildren() as $arg) {
                $arguments->add($arg);
            }
        } else if ($right instanceof IdentifierNode) {
            $right = new FunctionNameNode($right->getName());
        }

        return new FunctionCallNode($right, $arguments);
    }
}