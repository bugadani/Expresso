<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Operators\Ternary\ConditionalOperator as TernaryConditionalOperator;

class ConditionalOperator extends BinaryOperator
{

    public function operators()
    {
        return '?:';
    }

    public function createNode(CompilerConfiguration $config, Node $left, Node $right)
    {
        $ternaryConditionalOperator = $config->getOperatorByClass(TernaryConditionalOperator::class);

        return $ternaryConditionalOperator->createNode($config, $left, $left, $right);
    }
}