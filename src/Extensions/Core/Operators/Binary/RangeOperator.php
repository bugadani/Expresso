<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class RangeOperator extends BinaryOperator
{

    public function operators()
    {
        return '..';
    }

    public function execute(EvaluationContext $context, Node $left, Node $right)
    {
        return range(
            $left->evaluate($context),
            $right->evaluate($context)
        );
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('range(')
            ->compileNode($left)
            ->add(',')
            ->compileNode($right)
            ->add(')');
    }
}