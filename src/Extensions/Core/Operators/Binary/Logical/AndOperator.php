<?php

namespace Expresso\Extensions\Core\Operators\Binary\Logical;

use Expresso\Compiler\Compiler;

use Expresso\Compiler\Node;

use Expresso\Compiler\Operators\BinaryOperator;
use Expresso\EvaluationContext;

class AndOperator extends BinaryOperator
{

    public function operators()
    {
        return '&&';
    }

    public function evaluate(EvaluationContext $context, Node $node)
    {
        //This implements short-circuit evaluation
        $first  = (yield $node->getChildAt(0)->evaluate($context));
        $second = $first && (yield $node->getChildAt(1)->evaluate($context));
        $context->setReturnValue($second);
    }

    public function compile(Compiler $compiler, Node $node)
    {
        $compiler->add('(')
                 ->compileNode($node->getChildAt(0))
                 ->add('&&')
                 ->compileNode($node->getChildAt(1))
                 ->add(')');
    }
}