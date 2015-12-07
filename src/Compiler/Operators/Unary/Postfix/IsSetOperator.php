<?php

namespace Expresso\Compiler\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;


class IsSetOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is set';
    }

    public function execute(EvaluationContext $context, Node $operand)
    {
        /** @var IdentifierNode $operand */
        return $context->offsetExists($operand->getName());
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        /** @var IdentifierNode $operand */
        $compiler->add('$context->offsetExists(')
                 ->compileString($operand->getName())
                 ->add(')');
    }
}