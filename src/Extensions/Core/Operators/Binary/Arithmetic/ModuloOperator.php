<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Operators\BinaryOperator;

class ModuloOperator extends BinaryOperator
{

    public function operators()
    {
        return 'mod';
    }

    protected function evaluateSimple($left, $right)
    {
        if ($left < 0 && $right >= 0 || $left >= 0 && $right < 0) {
            return $right + $left % $right;
        } else {
            return $left % $right;
        }
    }

    /**
     * @param Compiler $compiler
     * @param $leftSource
     * @param $rightSource
     */
    protected function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add('((');
        $compiler->add($leftSource);
        $compiler->add(' < 0 && ');
        $compiler->add($rightSource);
        $compiler->add(' > 0) || (');
        $compiler->add($rightSource);
        $compiler->add(' < 0 && ');
        $compiler->add($leftSource);
        $compiler->add(' > 0)');

        //then $right + $left % right
        $compiler->add(' ? (');
        $compiler->add($rightSource);
        $compiler->add(' + ');
        $compiler->add($leftSource);
        $compiler->add(' % ');
        $compiler->add($rightSource);
        $compiler->add(')');

        //else $left % $right
        $compiler->add(' : (');
        $compiler->add($leftSource);
        $compiler->add(' % ');
        $compiler->add($rightSource);
        $compiler->add('))');
    }
}