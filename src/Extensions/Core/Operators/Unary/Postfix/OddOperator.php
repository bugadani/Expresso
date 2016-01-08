<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
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
        /** @var UnaryOperatorNode $node */
        yield $compiler->compileNode($node->getOperand());
        $compiler->add(' & 0x01) == 1');
    }
}