<?php

namespace Expresso\Extensions\Core\Operators\Binary\Test;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Operators\Unary\Prefix\NotOperator;

class NotDivisibleOperator extends DivisibleOperator
{

    public function operators()
    {
        return 'is not divisible by';
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