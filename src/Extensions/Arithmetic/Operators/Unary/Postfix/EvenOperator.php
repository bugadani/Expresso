<?php

namespace Expresso\Extensions\Arithmetic\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\EvaluationContext;

class EvenOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is even';
    }

    public function evaluateSimple($operand)
    {
        return ($operand & 0x01) == 0;
    }

    public function compile(Compiler $compiler, Node $operand)
    {
        $compiler->add('(')
                 ->compileNode($operand)
                 ->add(' & 0x01) == 0');
    }
}