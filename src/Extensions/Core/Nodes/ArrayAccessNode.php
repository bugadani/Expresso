<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\Exceptions\AssignmentException;
use Expresso\Runtime\ExecutionContext;

class ArrayAccessNode extends AccessNode
{
    public function compileAssign(Compiler $compiler, Node $rightHand)
    {
        if (!$this->left instanceof AssignableNode) {
            throw new AssignmentException('Cannot assign to non-variable');
        }
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

    protected function &get(&$container, $rightHand)
    {
        return $container[ $rightHand ];
    }

    protected function assign(&$container, $leftHand, $rightHand)
    {
        $container[ $leftHand ] = $rightHand;
    }

    protected function contains($container, $leftHand) : bool
    {
        return isset($container[ $leftHand ]);
    }

    public function compileContains(Compiler $compiler)
    {
        $compiler->add('isset(')
                 ->add(yield $compiler->compileNode($this))
                 ->add(')');
    }
}