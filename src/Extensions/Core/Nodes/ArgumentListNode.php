<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class ArgumentListNode extends Node
{
    /**
     * @var Node[]
     */
    private $arguments = [];

    public function getChildren() : array
    {
        return $this->arguments;
    }

    public function compile(Compiler $compiler)
    {
        if (!empty($this->arguments)) {
            $children  = $this->arguments;
            $lastChild = array_pop($children);

            foreach ($children as $child) {
                $compiledChild = (yield $compiler->compileNode($child));
                $compiler->add($compiledChild);
                $compiler->add(', ');
            }
            $compiledChild = (yield $compiler->compileNode($lastChild));
            $compiler->add($compiledChild);
        }
    }

    public function evaluate(EvaluationContext $context)
    {
        $list = [];
        foreach ($this->arguments as $child) {
            $list[] = (yield $child->evaluate($context));
        }
        return $list;
    }

    public function add(Node $node)
    {
        $this->arguments[] = $node;
    }
}