<?php

namespace Expresso\Compiler\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\InfiniteRangeIterator;

class IsNotSetOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is not set';
    }

    public function execute(EvaluationContext $context, NodeInterface $operand)
    {
        /** @var IdentifierNode $operand */
        return !$context->offsetExists($operand->getName());
    }

    public function compile(Compiler $compiler, NodeInterface $operand)
    {
        /** @var IdentifierNode $operand */
        $compiler->add('!$context->offsetExists(')
                 ->compileString($operand->getName())
                 ->add(')');
    }
}