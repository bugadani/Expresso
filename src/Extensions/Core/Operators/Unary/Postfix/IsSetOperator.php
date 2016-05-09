<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\UnaryOperatorNode;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;


class IsSetOperator extends UnaryOperator
{

    public function evaluate(EvaluationContext $context, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        return $context->offsetExists($node->getOperand()->getName());
    }

    public function compile(Compiler $compiler, Node $node)
    {
        /** @var UnaryOperatorNode $node */
        $compiler->add('$context->offsetExists(')
                 ->compileString($node->getOperand()->getName())
                 ->add(')');
    }
}