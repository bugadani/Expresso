<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class IntegerDivisionOperator extends BinaryOperator
{

    public function operators()
    {
        return 'div';
    }

    public function evaluateSimple($left, $right)
    {
        return ($left - $left % $right) / $right;
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('((')
                 ->compileNode($left)
                 ->add(' - ')
                 ->compileNode($left)
                 ->add(' % ')
                 ->compileNode($right)
                 ->add(') / ')
                 ->compileNode($right)
                 ->add(')');
    }
}