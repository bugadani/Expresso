<?php

namespace Expresso\Extensions\Arithmetic\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class NotDivisibleOperator extends BinaryOperator
{

    public function operators()
    {
        return 'is not divisible by';
    }

    public function execute(EvaluationContext $context, Node $left, Node $right)
    {
        return $left->evaluate($context) % $right->evaluate($context) !== 0;
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add('%')
                 ->compileNode($right)
                 ->add(' !== 0')
                 ->add(')');
    }
}