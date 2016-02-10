<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\UnaryOperator;

class InfiniteRangeOperator extends UnaryOperator
{

    public function operators()
    {
        return '...';
    }

    public function evaluateSimple($operand)
    {
        return new \IteratorIterator(\Expresso\Extensions\Core\range($operand));
    }

    protected function compileSimple(Compiler $compiler, $compiledSource)
    {
        $compiler->add("\\Expresso\\Extensions\\Core\\range({$compiledSource})");
    }
}