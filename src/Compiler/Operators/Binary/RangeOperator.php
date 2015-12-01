<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class RangeOperator extends BinaryOperator
{

    public function operators()
    {
        return '..';
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        return range(
            $left->evaluate($context),
            $right->evaluate($context)
        );
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        $compiler->add('range(')
            ->compileNode($left)
            ->add(',')
            ->compileNode($right)
            ->add(')');
    }
}