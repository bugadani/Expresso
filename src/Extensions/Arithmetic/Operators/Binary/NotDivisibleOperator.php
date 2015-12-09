<?php

namespace Expresso\Extensions\Arithmetic\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Logical\Operators\Unary\Prefix\NotOperator;

class NotDivisibleOperator extends BinaryOperator
{

    public function operators()
    {
        return 'is not divisible by';
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
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