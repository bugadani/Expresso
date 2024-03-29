<?php

namespace Expresso\Extensions\Core\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\UnaryOperator;

class BitwiseNotOperator extends UnaryOperator
{

    public function evaluateSimple($operand)
    {
        return ~$operand;
    }

    protected function compileSimple(Compiler $compiler, $compiledSource)
    {
        $compiler->add("~{$compiledSource}");
    }
}