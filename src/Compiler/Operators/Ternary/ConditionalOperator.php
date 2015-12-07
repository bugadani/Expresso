<?php

namespace Expresso\Compiler\Operators\Ternary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\TernaryOperator;
use Expresso\EvaluationContext;

class ConditionalOperator extends TernaryOperator
{

    public function operators()
    {
        return '?:';
    }

    public function execute(EvaluationContext $context, Node $left, Node $middle, Node $right)
    {
        return $left->evaluate($context) ? $middle->evaluate($context) : $right->evaluate($context);
    }

    public function compile(Compiler $compiler, Node $left, Node $middle, Node $right)
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