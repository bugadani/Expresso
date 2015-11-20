<?php

namespace Expresso\Compiler\Operators;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;

class MultiplicationOperator extends BinaryOperator
{

    public function operators()
    {
        return '*';
    }

    public function execute($left, $right)
    {
        return $left * $right;
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        $compiler->compileNode($left)->add(' * ')->compileNode($right);
    }
}