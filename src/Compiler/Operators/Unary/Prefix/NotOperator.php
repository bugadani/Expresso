<?php

namespace Expresso\Compiler\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\UnaryOperator;

class NotOperator extends UnaryOperator
{

    public function operators()
    {
        return '!';
    }

    public function execute($operand)
    {
        return !$operand;
    }

    public function compile(Compiler $compiler, NodeInterface $operand)
    {
        $compiler->add('!')
                 ->compileNode($operand);
    }
}