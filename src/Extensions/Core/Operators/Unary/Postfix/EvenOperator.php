<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Operators\UnaryOperator;

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

    protected function compileSimple(Compiler $compiler, $compiledSource)
    {
        $compiler->add('(')
                 ->add($compiledSource)
                 ->add(' & 0x01) == 0');
    }
}