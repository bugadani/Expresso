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
            $keys = [];
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

            $container =& $context[$containerNode->getName()];
            $varName = array_shift($keys);
            while(!empty($keys)) {
                $key = array_pop($keys);
                $container =& $container[$key];
            }
            $container[$varName] = $value;
        } else {
            throw new ParseException('');
        }

        yield $value;
    }

    public function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add("{$leftSource} = {$rightSource}");
    }
}