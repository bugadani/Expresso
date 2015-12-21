<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class InfiniteRangeOperator extends UnaryOperator
{

    public function operators()
    {
        return '...';
    }

    public function evaluateSimple($operand)
    {
        return \Expresso\Extensions\Core\range($operand);
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('\Expresso\Extensions\Core\range(');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(')');
    }
}