<?php

namespace Expresso\Extensions\Core\Operators\Binary\Comparison;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class NotEqualsOperator extends BinaryOperator
{

    public function operators()
    {
        return '!=';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $notOperator    = $config->getOperatorByClass(NotOperator::class);
        $equalsOperator = $config->getOperatorByClass(EqualsOperator::class);

        return $notOperator->createNode(
            $config,
            $equalsOperator->createNode($config, $left, $right)
        );
    }
}