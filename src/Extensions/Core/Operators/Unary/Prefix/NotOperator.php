<?php

namespace Expresso\Extensions\Core\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operators\UnaryOperator;

class NotOperator extends UnaryOperator
{

    public function operators()
    {
        return '!';
    }

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $compiledOperand = (yield $compiler->compileNode($node->getOperand()));

        $compiler->add('!');
        $compiler->add($compiledOperand->source);
    }

    public function evaluateSimple($left)
    {
        return !$left;
    }
}