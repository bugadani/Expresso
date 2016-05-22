<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Nodes\ArrayAccessNode;
use Expresso\Extensions\Core\Nodes\ArrayDataNode;
use Expresso\Extensions\Core\Nodes\MapDataNode;
use Expresso\Extensions\Core\Nodes\PropertyAccessNode;
use Expresso\Extensions\Core\Nodes\StatementNode;
use Expresso\Extensions\Core\Nodes\VariableNode;
use Expresso\Runtime\Exceptions\AssignmentException;
use Expresso\Runtime\ExecutionContext;

class AssignmentOperator extends BinaryOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands) : Node
    {
        list($structure, $source) = $operands;
        if ($structure instanceof ArrayDataNode) {
            $nodes = [];
            if ($structure instanceof MapDataNode) {
                foreach ($structure as $key => $variableToAssign) {
                    $nodes[] = $this->createNode($config, $variableToAssign, new PropertyAccessNode($source, $key));
                }
            } else {
                foreach ($structure as $key => $variableToAssign) {
                    $nodes[] = $this->createNode($config, $variableToAssign, new ArrayAccessNode($source, $key));
                }
            }

            return new StatementNode($nodes);
        } else {
            return parent::createNode($config, ...$operands);
        }
    }

    /**
     * @inheritdoc
     */
    public function evaluate(ExecutionContext $context, Node $node)
    {
        /** @var BinaryOperatorNode $node */
        $containerNode = $node->getLeft();

        if ($containerNode instanceof VariableNode) {
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

        if ($left instanceof VariableNode) {
            return $left->compileAssign($compiler, $right);
        } else {
            throw new AssignmentException('Can only assign to array element or object property');
        }
    }
}