<?php

namespace Expresso\Extensions\Arithmetic\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class MinusOperator extends UnaryOperator
{

    public function operators()
    {
        return '-';
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('-')
                 ->compileNode($operand);
    }

    public function evaluateSimple($left)
    {
        return -$left;
    }
}