<?php

namespace Expresso\Extensions\Logical\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class AndOperator extends BinaryOperator
{

    public function operators()
    {
        return '&&';
    }

    public function evaluate(EvaluationContext $context, Node $left, Node $right)
    {
        return $left->evaluate($context) && $right->evaluate($context);
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add('&&')
                 ->compileNode($right)
                 ->add(')');
    }
}