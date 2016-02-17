<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class IsNotSetOperator extends IsSetOperator
{

    public function createNode(CompilerConfiguration $config, $operand)
    {
        $notOperator = $config->getOperatorByClass(NotOperator::class);

        return $notOperator->createNode(
            $config,
            parent::createNode($config, $operand)
        );
    }
}