<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class ArrayAccessOperator extends BinaryOperator
{

    public function evaluate(EvaluationContext $context, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $left  = (yield $left->evaluate($context));
        $right = (yield $right->evaluate($context));

        return $context->access($left, $right);
    }

    public function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add("\$context->access({$leftSource}, {$rightSource})");
    }
}