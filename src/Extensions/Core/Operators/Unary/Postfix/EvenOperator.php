<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;

class EvenOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is even';
    }

    public function evaluateSimple($operand)
    {
        return ($operand & 0x01) == 0;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('(');
        yield $compiler->compileNode($node->getOperand());
        $compiler->add(' & 0x01) == 0');
    }
}