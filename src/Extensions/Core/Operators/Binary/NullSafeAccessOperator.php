<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Ternary\ConditionalOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsNotSetOperator;
use Expresso\Extensions\Logical\Operators\Binary\OrOperator;

class NullSafeAccessOperator extends BinaryOperator
{

    public function operators()
    {
        return '?.';
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        $conditionalOperator  = $config->getOperatorByClass(ConditionalOperator::class);
        $orOperator           = $config->getOperatorByClass(OrOperator::class);
        $isNotSetOperator     = $config->getOperatorByClass(IsNotSetOperator::class);
        $identicalOperator    = $config->getOperatorByClass(IdenticalOperator::class);
        $simpleAccessOperator = $config->getOperatorByClass(SimpleAccessOperator::class);

        return new TernaryOperatorNode(
            $conditionalOperator,
            $orOperator->createNode(
                $config,
                $left instanceof IdentifierNode ? $isNotSetOperator->createNode($config, $left) : $left,
                $identicalOperator->createNode($config, $left, DataNode::nullNode())
            ),
            new DataNode(null),
            $simpleAccessOperator->createNode($config, $left, $right)
        );
    }

    public function evaluate(EvaluationContext $context, Node $left, Node $right)
    {
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
    }
}