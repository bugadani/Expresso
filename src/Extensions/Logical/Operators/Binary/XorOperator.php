<?php

namespace Expresso\Extensions\Logical\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class XorOperator extends BinaryOperator
{
    public function operators()
    {
        return 'xor';
    }

    public function evaluate(EvaluationContext $context, Node $left, Node $right)
    {
        $left  = $left->evaluate($context);
        $right = $right->evaluate($context);

        return ($left || $right) && !($left && $right);
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('((')
                 ->compileNode($left)
                 ->add('||')
                 ->compileNode($right)
                 ->add(') && !(')
                 ->compileNode($left)
                 ->add('&&')
                 ->compileNode($right)
                 ->add('))');
    }
}