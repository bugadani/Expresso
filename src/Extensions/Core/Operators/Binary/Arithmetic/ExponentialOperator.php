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

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('pow(')
                 ->compileNode($node->getChildAt(0))
                 ->add(', ')
                 ->compileNode($node->getChildAt(1))
                 ->add(')');
    }
}