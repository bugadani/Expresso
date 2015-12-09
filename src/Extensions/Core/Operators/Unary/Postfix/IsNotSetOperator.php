<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\Extensions\Logical\Operators\Unary\Prefix\NotOperator;


class IsNotSetOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is not set';
    }

    public function createNode(CompilerConfiguration $config, $operand)
    {
        $notOperator   = $config->getOperatorByClass(NotOperator::class);
        $isSetOperator = $config->getOperatorByClass(IsSetOperator::class);

        return $notOperator->createNode(
            $config,
            $isSetOperator->createNode($config, $operand)
        );
    }

    public function compile(Compiler $compiler, Node $operand)
    {
    }
}