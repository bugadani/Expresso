<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\ExecutionContext;

class ArgumentListNode extends Node
{
    /**
     * @var Node[]
     */
    private $arguments = [];

    public function compile(Compiler $compiler)
    {
        if (!empty($this->arguments)) {
            $children  = $this->arguments;
            $lastChild = array_pop($children);

            foreach ($children as $child) {
                $compiler->add(yield $compiler->compileNode($child));
                $compiler->add(', ');
            }
            $compiler->add(yield $compiler->compileNode($lastChild));
        }
    }

    public function evaluate(ExecutionContext $context)
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

    public function getChildren() : array
    {
        return $this->arguments;
    }

    public function getCount()
    {
        return count($this->arguments);
    }
}
