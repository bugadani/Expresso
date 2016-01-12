<?php

namespace Expresso\Extensions\Core\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Operators\UnaryOperator;

class OddOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is odd';
    }

    public function evaluateSimple($operand)
    {
        return ($operand & 0x01) == 1;
    }

    protected function compileSimple(Compiler $compiler, $compiledSource)
    {
        $compiler->add('(')
                 ->add($compiledSource)
                 ->add(' & 0x01) == 1');
    }
}