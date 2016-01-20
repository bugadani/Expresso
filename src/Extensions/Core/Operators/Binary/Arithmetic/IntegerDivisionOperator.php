<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\BinaryOperator;

class IntegerDivisionOperator extends BinaryOperator
{

    public function operators()
    {
        return 'div';
    }

    protected function evaluateSimple($left, $right)
    {
        return ($left - $left % $right) / $right;
    }

    /**
     * @param Compiler $compiler
     * @param          $leftSource
     * @param          $rightSource
     */
    protected function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add('((');
        $compiler->add($leftSource);
        $compiler->add(' - ');
        $compiler->add($leftSource);
        $compiler->add(' % ');
        $compiler->add($rightSource);
        $compiler->add(') / ');
        $compiler->add($rightSource);
        $compiler->add(')');
    }
}