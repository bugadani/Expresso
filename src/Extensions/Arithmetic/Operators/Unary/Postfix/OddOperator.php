<?php

namespace Expresso\Extensions\Arithmetic\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\InfiniteRangeIterator;

class OddOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is odd';
    }

    public function evaluate(EvaluationContext $context, Node $operand)
    {
        return ($operand->evaluate($context) & 0x01) == 1;
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('(')
                 ->compileNode($operand)
                 ->add(' & 0x01) == 1');
    }
}