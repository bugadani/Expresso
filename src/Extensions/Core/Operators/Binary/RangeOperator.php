<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class RangeOperator extends BinaryOperator
{

    public function operators()
    {
        return '..';
    }

    public function evaluateSimple($left, $right)
    {
        return new \IteratorIterator(\Expresso\Extensions\Core\range($left, $right));
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

        $compiler->add('\Expresso\Extensions\Core\range(');
        $compiler->add($leftSource);
        $compiler->add(', ');
        $compiler->add($rightSource);
        $compiler->add(')');
    }
}