<?php

namespace Expresso\Compiler\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\InfiniteRangeIterator;

class InfiniteRangeOperator extends UnaryOperator
{

    public function operators()
    {
        return '...';
    }

    public function execute(EvaluationContext $context, Node $operand)
    {
        return new InfiniteRangeIterator($operand->evaluate($context));
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('new \Expresso\Extensions\Core\InfiniteRangeIterator(')
                 ->compileNode($operand)
                 ->add(')');
    }
}