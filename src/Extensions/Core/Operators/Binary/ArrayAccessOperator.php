<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\ExecutionContext;

class ArrayAccessOperator extends BinaryOperator
{

    public function evaluate(ExecutionContext $context, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $left  = (yield $left->evaluate($context));
        $right = (yield $right->evaluate($context));

        return ExecutionContext::access($left, $right);
    }

    public function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $class = ExecutionContext::class;
        $compiler->add("{$class}::access({$leftSource}, {$rightSource})");
    }
}