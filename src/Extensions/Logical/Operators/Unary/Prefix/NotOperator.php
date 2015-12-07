<?php

namespace Expresso\Extensions\Logical\Operators\Unary\Prefix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;

class NotOperator extends UnaryOperator
{

    public function operators()
    {
        return '!';
    }

    public function execute(EvaluationContext $context, Node $operand)
    {
        return !$operand->evaluate($context);
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('!')
                 ->compileNode($operand);
    }
}