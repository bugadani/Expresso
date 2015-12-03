<?php

namespace Expresso\Extensions\Bitwise\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\UnaryOperator;

class BitwiseNotOperator extends UnaryOperator
{

    public function operators()
    {
        return '~';
    }

    public function execute($operand)
    {
        return ~$operand;
    }

    public function compile(Compiler $compiler, NodeInterface $operand)
    {
        $compiler->add('~')
                 ->compileNode($operand);
    }
}