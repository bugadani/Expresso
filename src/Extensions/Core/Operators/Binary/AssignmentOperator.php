<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;

use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Runtime\ExecutionContext;
use Expresso\Extensions\Core\Nodes\IdentifierNode;

class AssignmentOperator extends BinaryOperator
{

    /**
     * @inheritdoc
     */
    public function evaluate(ExecutionContext $context, Node $node)
    {
        /** @var BinaryOperatorNode $node */
        $variableToSet = $node->getLeft();
        $value         = (yield $node->getRight()->evaluate($context));

        if ($variableToSet instanceof IdentifierNode) {
            //Assign to simple value
            $context[ $variableToSet->getName() ] = $value;
        } else if ($variableToSet instanceof BinaryOperatorNode) {
            $keys          = [];
            $containerNode = $variableToSet;

            //Collect keys
            do {
                $operator = $variableToSet->getOperator();

                if ($operator instanceof ArrayAccessOperator) {
                    list($containerNode, $index) = $containerNode->getChildren();

                    $keys[] = (yield $index->evaluate($context));
                } else {
                    break;
                }
            } while ($containerNode instanceof BinaryOperatorNode);

            //Get reference to indexed variable
            $container =& $context[ $containerNode->getName() ];
            $varName   = array_shift($keys);
            while (!empty($keys)) {
                $key       = array_pop($keys);
                $container =& ExecutionContext::access($container, $key);
            }

            //Assign value
            if (is_array($container) || $container instanceof \ArrayAccess) {
                $container[ $varName ] = $value;
            } else if (is_object($container)) {
                $container->{$varName} = $value;
            } else {
                throw new \UnexpectedValueException('Can only assign to array element or object property');
            }
        } else {
            throw new ParseException('Invalid left-hand expression in assignment');
        }

        return $value;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $tempVar = $compiler->requestTempVariable();

        $rightSource = (yield $compiler->compileNode($right));

        if ($left instanceof IdentifierNode) {
            $compiler->addVariableAccess($left->getName())
                     ->add(" = {$rightSource}");
        } else {
            $leftSource  = (yield $compiler->compileNode($left));
            $compiler->addStatement("{$tempVar} =& {$leftSource}");
            $compiler->add("{$tempVar} = {$rightSource}");
        }
    }
}