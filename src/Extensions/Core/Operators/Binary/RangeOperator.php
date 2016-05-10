<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\BinaryOperator;

class RangeOperator extends BinaryOperator
{

    protected function evaluateSimple($left, $right)
    {
        return \Expresso\Extensions\Core\range($left, $right);
    }

    /**
     * @param Compiler $compiler
     * @param          $leftSource
     * @param          $rightSource
     */
    protected function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add('\\Expresso\\Extensions\\Core\\range(')
                 ->add($leftSource)
                 ->add(', ')
                 ->add($rightSource)
                 ->add(')');
    }
}