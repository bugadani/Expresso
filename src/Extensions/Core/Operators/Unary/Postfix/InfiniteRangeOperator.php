<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operators\UnaryOperator;

class InfiniteRangeOperator extends UnaryOperator
{

    public function operators()
    {
        return '...';
    }

    public function evaluateSimple($operand)
    {
        return new \IteratorIterator(\Expresso\Extensions\Core\range($operand));
    }

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $compiledOperand = (yield $compiler->compileNode($node->getOperand()));

        if ($node->isInline()) {
            $compiledSource  = $compiledOperand->source;
        } else {
            $compiledSource  = $compiler->addTempVariable($compiledOperand);
        }

        $compiler->add('\Expresso\Extensions\Core\range(');
        $compiler->add($compiledSource);
        $compiler->add(')');
    }
}