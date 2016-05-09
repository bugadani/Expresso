<?php

namespace Expresso\Extensions\Core\Operators\Binary\Comparison;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class NotIdenticalOperator extends IdenticalOperator
{

    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($left, $right) = $operands;
        $notOperator = $config->getOperatorByClass(NotOperator::class);

        return $notOperator->createNode(
            $config,
            parent::createNode($config, $left, $right)
        );
    }
}