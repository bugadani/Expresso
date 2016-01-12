<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Operators\Binary\Comparison\IdenticalOperator;
use Expresso\Extensions\Core\Operators\Binary\Logical\OrOperator;
use Expresso\Extensions\Core\Operators\Ternary\ConditionalOperator as TernaryConditionalOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\IsNotSetOperator;

class NullSafeAccessOperator extends BinaryOperator
{

    public function operators()
    {
        return '?.';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $conditionalOperator  = $config->getOperatorByClass(TernaryConditionalOperator::class);
        $orOperator           = $config->getOperatorByClass(OrOperator::class);
        $isNotSetOperator     = $config->getOperatorByClass(IsNotSetOperator::class);
        $identicalOperator    = $config->getOperatorByClass(IdenticalOperator::class);
        $simpleAccessOperator = $config->getOperatorByClass(SimpleAccessOperator::class);

        return $conditionalOperator->createNode(
            $config,
            $orOperator->createNode(
                $config,
                $left instanceof IdentifierNode ? $isNotSetOperator->createNode($config, $left) : $left,
                $identicalOperator->createNode($config, $left, new DataNode(null))
            ),
            new DataNode(null),
            $simpleAccessOperator->createNode($config, $left, $right)
        );
    }
}