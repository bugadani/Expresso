<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class FilterOperator extends BinaryOperator
{

    public function operators()
    {
        return '|';
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        return new FunctionCallNode($right, [$left]);
    }
}