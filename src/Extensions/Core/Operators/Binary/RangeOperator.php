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
        list($left, $right) = $node->getChildren();

        $compiler->add('\Expresso\Extensions\Core\range(');
        yield $compiler->compileNode($left);
        $compiler->add(', ');
        yield $compiler->compileNode($right);
        $compiler->add(')');
    }
}