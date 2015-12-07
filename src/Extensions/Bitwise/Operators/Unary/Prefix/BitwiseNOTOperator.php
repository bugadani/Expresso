<?php

namespace Expresso\Extensions\Bitwise\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;

class BitwiseNotOperator extends UnaryOperator
{

    public function operators()
    {
        return '~';
    }

    public function evaluate(EvaluationContext $context, Node $operand)
    {
        return ~$operand->evaluate($context);
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('~')
                 ->compileNode($operand);
    }
}