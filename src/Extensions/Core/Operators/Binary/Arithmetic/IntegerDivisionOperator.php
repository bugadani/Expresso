<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\BinaryOperator;

class IntegerDivisionOperator extends BinaryOperator
{

    protected function evaluateSimple($left, $right)
    {
        return intdiv($left, $right);
    }

    /**
     * @param Compiler $compiler
     * @param          $leftSource
     * @param          $rightSource
     */
    protected function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add('\intdiv(')
                 ->add($leftSource)
                 ->add(', ')
                 ->add($rightSource)
                 ->add(')');
    }
}