<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\Extensions\Core\InfiniteRangeIterator;

class InfiniteRangeOperator extends UnaryOperator
{

    public function operators()
    {
        return '...';
    }

    public function evaluateSimple($operand)
    {
        return new InfiniteRangeIterator($operand);
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('new \Expresso\Extensions\Core\InfiniteRangeIterator(')
                 ->compileNode($operand)
                 ->add(')');
    }
}