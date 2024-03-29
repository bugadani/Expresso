<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\UnaryOperator;

class EvenOperator extends UnaryOperator
{

    public function evaluateSimple($operand)
    {
        return ($operand & 0x01) == 0;
    }

    protected function compileSimple(Compiler $compiler, $compiledSource)
    {
        $compiler->add("({$compiledSource} & 0x01) == 0");
    }
}