<?php

namespace Expresso\Extensions\Core\Operators\Binary\Comparison;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class NotEqualsOperator extends EqualsOperator
{

    public function operators()
    {
        return '!=';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $notOperator = $config->getOperatorByClass(NotOperator::class);

        return $notOperator->createNode(
            $config,
            parent::createNode($config, $left, $right)
        );
    }
}