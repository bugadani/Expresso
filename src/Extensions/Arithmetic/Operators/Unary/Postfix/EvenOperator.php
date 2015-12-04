<?php

namespace Expresso\Extensions\Arithmetic\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;

class EvenOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is even';
    }

    public function execute(EvaluationContext $context, NodeInterface $operand)
    {
        return ($operand->evaluate($context) & 0x01) == 0;
    }

    public function compile(Compiler $compiler, NodeInterface $operand)
    {
        $compiler->add('(')
                 ->compileNode($operand)
                 ->add(' & 0x01) == 0');
    }
}