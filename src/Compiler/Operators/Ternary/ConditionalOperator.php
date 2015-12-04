<?php

namespace Expresso\Compiler\Operators\Ternary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;

class ConditionalOperator extends TernaryOperator
{

    public function operators()
    {

    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $middle, NodeInterface $right)
    {
        return $left->evaluate($context) ? $middle->evaluate($context) : $right->evaluate($context);
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $middle, NodeInterface $right)
    {
        $compiler->add('((')
            ->compileNode($left)
            ->add(') ? (')
            ->compileNode($middle)
            ->add(') : (')
            ->compileNode($right)
            ->add('))');
    }
}