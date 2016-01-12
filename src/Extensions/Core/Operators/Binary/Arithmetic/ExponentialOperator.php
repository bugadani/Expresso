<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Operators\BinaryOperator;

class ExponentialOperator extends BinaryOperator
{

    public function operators()
    {
        return '^';
    }

    protected function evaluateSimple($left, $right)
    {
        return pow($left, $right);
    }

    /**
     * @param Compiler $compiler
     * @param $leftSource
     * @param $rightSource
     */
    protected function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add('pow(')
                 ->add($leftSource)
                 ->add(', ')
                 ->add($rightSource)
                 ->add(')');
    }
}