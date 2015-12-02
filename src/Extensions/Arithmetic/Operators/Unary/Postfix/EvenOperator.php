<?php

namespace Expresso\Extensions\Arithmetic\Operators\Unary\Postfix;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\UnaryOperator;

class EvenOperator extends UnaryOperator
{

    public function operators()
    {
        return 'is even';
    }

    public function execute($operand)
    {
        return ($operand & 0x01) == 0;
    }

    public function compile(Compiler $compiler, NodeInterface $operand)
    {
        $compiler->add('(')
                 ->compileNode($operand)
                 ->add(' & 0x01) == 0');
    }
}