<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class OddOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is odd';
    }

    public function evaluateSimple($operand)
    {
        return ($operand & 0x01) == 1;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('(');
        yield $node->getChildAt(0)->compile($compiler);
        $compiler->add(' & 0x01) == 1');
    }
}