<?php

namespace Expresso\Extensions\Arithmetic\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class NotDivisibleOperator extends BinaryOperator
{

    public function operators()
    {
        return 'is not divisible by';
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        return $left->evaluate($context) % $right->evaluate($context) !== 0;
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add('%')
                 ->compileNode($right)
                 ->add(' !== 0')
                 ->add(')');
    }
}