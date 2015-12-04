<?php

namespace Expresso\Extensions\Bitwise\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;

class BitwiseNotOperator extends UnaryOperator
{

    public function operators()
    {
        return '~';
    }

    public function execute(EvaluationContext $context, NodeInterface $operand)
    {
        return ~$operand->evaluate($context);
    }

    public function compile(Compiler $compiler, NodeInterface $operand)
    {
        $compiler->add('~')
                 ->compileNode($operand);
    }
}