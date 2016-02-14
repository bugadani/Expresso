<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Nodes\IdentifierNode;

class AssignmentOperator extends BinaryOperator
{

    public function operators()
    {
        return ':=';
    }

    /**
     * @inheritdoc
     */
    public function evaluate(EvaluationContext $context, Node $node)
    {
        /** @var BinaryOperatorNode $node */
        $variableToSet = $node->getLeft();
        $value         = (yield $node->getRight()->evaluate($context));

        if ($variableToSet instanceof IdentifierNode) {
            $context[ $variableToSet->getName() ] = $value;
        } else if ($variableToSet instanceof BinaryOperatorNode) {
            $keys          = [];
            $containerNode = $variableToSet;
            do {
                $operator = $variableToSet->getOperator();

                if ($operator instanceof ArrayAccessOperator) {
                    list($containerNode, $index) = $containerNode->getChildren();

                    $keys[] = (yield $index->evaluate($context));
                } else {
                    break;
                }
            } while ($containerNode instanceof BinaryOperatorNode);

            $container =& $context[ $containerNode->getName() ];
            $varName   = array_shift($keys);
            while (!empty($keys)) {
                $key       = array_pop($keys);
                $container =& $context->access($container, $key);
            }
            if (is_array($container) || $container instanceof \ArrayAccess) {
                $container[ $varName ] = $value;
            } else if (is_object($container)) {
                $container->{$varName} = $value;
            } else {
                throw new \UnexpectedValueException();
            }
        } else {
            throw new ParseException('');
        }

        yield $value;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $tempVar = $compiler->requestTempVariable();

        if ($left instanceof IdentifierNode) {
            $rightSource = (yield $compiler->compileNode($right));
            $compiler->addVariableAccess($left->getName())
                     ->add(" = {$rightSource}");
        } else {
            $leftSource  = (yield $compiler->compileNode($left));
            $rightSource = (yield $compiler->compileNode($right));

            $compiler->addStatement("{$tempVar} =& {$leftSource}");
            $compiler->add("{$tempVar} = {$rightSource}");
        }
    }
}