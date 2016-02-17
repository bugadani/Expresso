<?php

namespace Expresso\Extensions\Core\Operators\Binary\Test;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\BinaryOperator;

class DivisibleOperator extends BinaryOperator
{

    protected function evaluateSimple($left, $right)
    {
        return $left % $right === 0;
    }

    /**
     * @param Compiler $compiler
     * @param          $leftSource
     * @param          $rightSource
     */
    protected function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add('(')
                 ->add($leftSource)
                 ->add('%')
                 ->add($rightSource)
                 ->add(' === 0)');
    }
}