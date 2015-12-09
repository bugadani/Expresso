<?php

namespace Expresso\Extensions\Logical\Operators\Binary;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Logical\Operators\Unary\Prefix\NotOperator;

class XorOperator extends BinaryOperator
{
    public function operators()
    {
        return 'xor';
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        $orOperator  = $config->getOperatorByClass(OrOperator::class);
        $andOperator = $config->getOperatorByClass(AndOperator::class);
        $notOperator = $config->getOperatorByClass(NotOperator::class);

        //(left || right) && !(left && right)
        return $andOperator->createNode(
            $config,
            $orOperator->createNode($config, $left, $right),
            $notOperator->createNode(
                $config,
                $andOperator->createNode($config, $left, $right)
            )
        );
    }
}