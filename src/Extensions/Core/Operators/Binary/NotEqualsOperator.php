<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Logical\Operators\Unary\Prefix\NotOperator;

class NotEqualsOperator extends BinaryOperator
{

    public function operators()
    {
        return '!=';
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        $notOperator    = $config->getOperatorByClass(NotOperator::class);
        $equalsOperator = $config->getOperatorByClass(EqualsOperator::class);

        return $notOperator->createNode(
            $config,
            $equalsOperator->createNode($config, $left, $right)
        );
    }
}