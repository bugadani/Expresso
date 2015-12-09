<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Logical\Operators\Unary\Prefix\NotOperator;

class NotIdenticalOperator extends BinaryOperator
{

    public function operators()
    {
        return '!==';
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        $notOperator       = $config->getOperatorByClass(NotOperator::class);
        $identicalOperator = $config->getOperatorByClass(IdenticalOperator::class);

        return $notOperator->createNode(
            $config,
            $identicalOperator->createNode($config, $left, $right)
        );
    }
}