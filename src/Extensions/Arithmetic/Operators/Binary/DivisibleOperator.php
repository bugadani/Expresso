<?php

namespace Expresso\Extensions\Arithmetic\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class DivisibleOperator extends BinaryOperator
{

    public function operators()
    {
        return 'is divisible by';
    }

    public function evaluateSimple($left, $right)
    {
        return $left % $right === 0;
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('(')
                 ->compileNode($left)
                 ->add('%')
                 ->compileNode($right)
                 ->add(' === 0')
                 ->add(')');
    }
}