<?php

namespace Expresso\Extensions\Core\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\UnaryOperator;

class NotOperator extends UnaryOperator
{

    public function operators()
    {
        return '!';
    }

    protected function compileSimple(Compiler $compiler, $compiledSource)
    {
        $compiler->add("!{$compiledSource}");
    }

    public function evaluateSimple($left)
    {
        return !$left;
    }
}