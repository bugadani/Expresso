<?php

namespace Expresso\Extensions\Core\Operators\Binary\Logical;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\Runtime\ExecutionContext;

class AndOperator extends BinaryOperator
{

    public function evaluate(ExecutionContext $context, Node $node)
    {
        /** @var Node $left */
        /** @var Node $right */
        list($left, $right) = $node->getChildren();

        //This implements short-circuit evaluation
        $first  = (yield $left->evaluate($context));
        $second = $first && (yield $right->evaluate($context));
        return $second;
    }

    public function compiledOperator()
    {
        return '&&';
    }
}
