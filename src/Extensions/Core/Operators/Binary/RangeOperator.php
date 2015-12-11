<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class RangeOperator extends BinaryOperator
{

    public function operators()
    {
        return '..';
    }

    public function evaluateSimple($left, $right)
    {
        return \Expresso\Extensions\Core\range($left, $right);
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
    {
        $compiler->add('\Expresso\Extensions\Core\range(')
                 ->compileNode($left)
                 ->add(',')
                 ->compileNode($right)
                 ->add(')');
    }
}