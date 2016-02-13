<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\BinaryOperator;

class ArrayAccessOperator extends BinaryOperator
{

    public function operators()
    {
    }

    protected function evaluateSimple($left, $right)
    {
        return $left[ $right ];
    }

    public function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $compiler->add("{$leftSource}[{$rightSource}]");
    }
}