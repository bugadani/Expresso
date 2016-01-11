<?php

namespace Expresso\Extensions\Core\Operators\Binary\Arithmetic;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\BinaryOperator;

class ModuloOperator extends BinaryOperator
{

    public function operators()
    {
        return 'mod';
    }

    public function evaluateSimple($left, $right)
    {
        if ($left < 0 && $right >= 0 || $left >= 0 && $right < 0) {
            return $right + $left % $right;
        } else {
            return $left % $right;
        }
    }

    public function compile(Compiler $compiler, Node $node)
    {
        //if(sign($left) != sign($right))
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
        $compiler->add(' < 0 && ');
        $compiler->add($rightSource);
        $compiler->add(' > 0) || (');
        $compiler->add($rightSource);
        $compiler->add(' < 0 && ');
        $compiler->add($leftSource);
        $compiler->add(' > 0)');

        //then $right + $left % right
        $compiler->add(' ? (');
        $compiler->add($rightSource);
        $compiler->add(' + ');
        $compiler->add($leftSource);
        $compiler->add(' % ');
        $compiler->add($rightSource);
        $compiler->add(')');

        //else $left % $right
        $compiler->add(' : (');
        $compiler->add($leftSource);
        $compiler->add(' % ');
        $compiler->add($rightSource);
        $compiler->add('))');
    }
}