<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Nodes\AssignableNode;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
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
        $value         = (yield $node->getRight()->evaluate($context));

        if ($containerNode instanceof AssignableNode) {
            //Assign to simple value
            yield $containerNode->evaluateAssign($context, $value);
        } else if ($containerNode instanceof BinaryOperatorNode) {
            $keys = new \SplStack();

            if (!$containerNode->isOperator(ArrayAccessOperator::class)) {
                throw new AssignmentException('Can only assign to array element or object property');
            }
            list($containerNode, $index) = $containerNode->getChildren();
            $varName = (yield $index->evaluate($context));

            //Collect keys
            while ($containerNode instanceof BinaryOperatorNode && $containerNode->isOperator(ArrayAccessOperator::class)) {
                list($containerNode, $index) = $containerNode->getChildren();

                $keys->push(yield $index->evaluate($context));
            }

            //Get reference to indexed variable
            $container =& $context[ $containerNode->getName() ];
            foreach ($keys as $key) {
                $container =& ExecutionContext::access($container, $key);
            }

            //Assign value
            if (is_array($container) || $container instanceof \ArrayAccess) {
                $container[ $varName ] = $value;
            } else if (is_object($container)) {
                $container->{$varName} = $value;
            } else {
                throw new AssignmentException('Can only assign to array element or object property');
            }
        } else {
            throw new ParseException('Invalid left-hand expression in assignment');
        }

        return $value;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $right) = $node->getChildren();

        if ($left instanceof AssignableNode) {
            yield $left->compileAssign($compiler, $right);
        } else if ($left instanceof BinaryOperatorNode && $left->isOperator(ArrayAccessOperator::class)) {
            $rightSource = (yield $compiler->compileNode($right));
            $leftSource = (yield $compiler->compileNode($left));

            $tempVar = $compiler->requestTempVariable();
            $compiler->addStatement("{$tempVar} =& {$leftSource}");
            $compiler->add("{$tempVar} = {$rightSource}");
        } else {
            throw new AssignmentException('Can only assign to array element or object property');
        }
    }
}