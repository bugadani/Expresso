<?php

namespace Expresso\Extensions\Core\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operators\UnaryOperator;

class BitwiseNotOperator extends UnaryOperator
{

    public function operators()
    {
        return '~';
    }

    public function evaluateSimple($operand)
    {
        return ~$operand;
    }

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $compiledOperand = (yield $compiler->compileNode($node->getOperand()));

        $tempVariable = $compiler->addTempVariable($compiledOperand);

        $compiler->add('~');
        $compiler->add($tempVariable);
    }
}