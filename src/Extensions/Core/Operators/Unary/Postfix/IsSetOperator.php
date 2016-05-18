<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\Runtime\ExecutionContext;

class IsSetOperator extends UnaryOperator
{

    public function evaluate(ExecutionContext $context, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $operand = $node->getOperand();
        return $context->offsetExists($operand->getName());
    }

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $operand = $node->getOperand();
        $compiler->add('isset(')
                 ->add(yield $compiler->compileNode($operand))
                 ->add(')');
    }
}