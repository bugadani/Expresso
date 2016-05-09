<?php

namespace Expresso\Extensions\Core\Operators\Binary\Logical;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class OrOperator extends BinaryOperator
{

    public function evaluate(EvaluationContext $context, Node $node)
    {
        /** @var Node $left */
        /** @var Node $right */
        list($left, $right) = $node->getChildren();

        //This implements short-circuit evaluation
        $first  = (yield $left->evaluate($context));
        $second = $first || (yield $right->evaluate($context));
        return $second;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $leftOperand  = (yield $compiler->compileNode($left));
        $rightOperand = (yield $compiler->compileNode($right));

        $compiler->add("({$leftOperand}) || ({$rightOperand})");
    }
}
