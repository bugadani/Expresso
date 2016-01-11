<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class IntegerDivisionOperator extends BinaryOperator
{

    public function operators()
    {
        return 'div';
    }

    public function evaluateSimple($left, $right)
    {
        return ($left - $left % $right) / $right;
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

        $compiler->add('((');
        $compiler->add($leftSource);
        $compiler->add(' - ');
        $compiler->add($leftSource);
        $compiler->add(' % ');
        $compiler->add($rightSource);
        $compiler->add(') / ');
        $compiler->add($rightSource);
        $compiler->add(')');
    }
}