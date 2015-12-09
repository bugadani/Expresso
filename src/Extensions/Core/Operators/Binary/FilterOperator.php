<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Operators\BinaryOperator;

class FilterOperator extends BinaryOperator
{

    public function operators()
    {
        return '|';
    }

    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        if (!$right instanceof IdentifierNode) {
            throw new ParseException("The right hand operand of filter node must be a function name");
        }

        return new FunctionCallNode($right, [$left]);
    }
}