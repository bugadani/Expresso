<?php

namespace Expresso\Extensions\Core\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class BitwiseNotOperator extends UnaryOperator
{

    public function operators()
    {
        return '~';
    }

    public function evaluateSimple($operand)
    {
        return ~$operand;
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('~')
                 ->compileNode($operand);
    }
}