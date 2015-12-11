<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class ExponentialOperator extends BinaryOperator
{

    public function operators()
    {
        return '^';
    }

    public function evaluateSimple($left, $right)
    {
        return pow($left, $right);
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('pow(')
                 ->compileNode($left)
                 ->add(', ')
                 ->compileNode($right)
                 ->add(')');
    }
}