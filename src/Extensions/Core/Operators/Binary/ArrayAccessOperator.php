<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class ArrayAccessOperator extends BinaryOperator
{

    public function operators()
    {
    }

    public function evaluateSimple($left, $right)
    {
        return $left[ $right ];
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('$context->access(');
        yield $compiler->compileNode($node->getChildAt(0));
        $compiler->add(', ');
        yield $compiler->compileNode($node->getChildAt(1));
        $compiler->add(')');
    }
}