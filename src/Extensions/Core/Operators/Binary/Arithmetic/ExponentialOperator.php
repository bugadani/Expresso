<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class ExponentialOperator extends BinaryOperator
{

    public function operators()
    {
        return '^';
    }

    public function evaluateSimple($left, $right)
    {
        return pow($left, $right);
    }

    public function compile(Compiler $compiler, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $leftOperand = (yield $compiler->compileNode($left));
        $rightOperand = (yield $compiler->compileNode($right));

        if ($node->isInline()) {
            $leftSource  = $leftOperand->source;
            $rightSource = $rightOperand->source;
        } else {
            $leftSource  = $compiler->addTempVariable($leftOperand);
            $rightSource = $compiler->addTempVariable($rightOperand);
        }

        $compiler->add('pow(');
        $compiler->add($leftSource);
        $compiler->add(', ');
        $compiler->add($rightSource);
        $compiler->add(')');
    }
}