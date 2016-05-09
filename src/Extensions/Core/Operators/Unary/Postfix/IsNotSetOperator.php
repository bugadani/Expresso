<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class IsNotSetOperator extends IsSetOperator
{

    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($operand) = $operands;
        $notOperator = $config->getOperatorByClass(NotOperator::class);

        return $notOperator->createNode(
            $config,
            parent::createNode($config, $operand)
        );
    }
}