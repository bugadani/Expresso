<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Extensions\Core\Operators\Ternary\ConditionalOperator as TernaryConditionalOperator;

class ConditionalOperator extends BinaryOperator
{

    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($left, $right) = $operands;
        $ternaryConditionalOperator = $config->getOperatorByClass(TernaryConditionalOperator::class);

        return $ternaryConditionalOperator->createNode($config, $left, $left, $right);
    }
}