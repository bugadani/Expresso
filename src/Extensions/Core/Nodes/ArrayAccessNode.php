<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class ArrayAccessNode extends AccessNode
{
    public function compileAssign(Compiler $compiler, Node $rightHand)
    {
        $compiler->add(yield $compiler->compileNode($this))
                 ->add(' = ')
                 ->add(yield $compiler->compileNode($rightHand));
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add(yield $compiler->compileNode($this->left))
                 ->add('[')
                 ->add(yield $compiler->compileNode($this->right))
                 ->add(']');
    }

    public function evaluate(ExecutionContext $context)
    {
        $left  = (yield $this->left->evaluate($context));
        $right = (yield $this->right->evaluate($context));

        return $left[ $right ];
    }

    protected function &get(&$container, $rightHand, bool $forAssign)
    {
        return $container[$rightHand];
    }

    protected function assign(&$container, $leftHand, $rightHand)
    {
        $container[$leftHand] = $rightHand;
    }
}