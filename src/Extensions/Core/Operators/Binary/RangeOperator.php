<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class RangeOperator extends BinaryOperator
{

    public function operators()
    {
        return '..';
    }

    public function evaluateSimple($left, $right)
    {
        return new \IteratorIterator(\Expresso\Extensions\Core\range($left, $right));
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('\Expresso\Extensions\Core\range(');
        yield $compiler->compileNode($node->getChildAt(0));
        $compiler->add(', ');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add(')');
    }
}