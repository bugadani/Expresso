<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Nodes\AssignableNode;
use Expresso\Runtime\Exceptions\AssignmentException;
use Expresso\Runtime\ExecutionContext;

class AssignmentOperator extends BinaryOperator
{

    /**
     * @inheritdoc
     */
    public function evaluate(ExecutionContext $context, Node $node)
    {
        /** @var BinaryOperatorNode $node */
        $containerNode = $node->getLeft();

        if ($containerNode instanceof AssignableNode) {
            //Assign to simple value
            $value = (yield $node->getRight()->evaluate($context));
            yield $containerNode->evaluateAssign($context, $value);

            return $value;
        } else {
            throw new AssignmentException('Invalid left-hand expression in assignment');
        }
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $right) = $node->getChildren();

        if ($left instanceof AssignableNode) {
            return $left->compileAssign($compiler, $right);
        } else {
            throw new AssignmentException('Can only assign to array element or object property');
        }
    }
}