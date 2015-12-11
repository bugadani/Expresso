<?php

namespace Expresso\Extensions\Core\Operators\Binary\Test;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class NotDivisibleOperator extends BinaryOperator
{

    public function operators()
    {
        return 'is not divisible by';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $notOperator   = $config->getOperatorByClass(NotOperator::class);
        $isSetOperator = $config->getOperatorByClass(DivisibleOperator::class);

        return $notOperator->createNode(
            $config,
            $isSetOperator->createNode($config, $left, $right)
        );
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
    }
}