<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class ArgumentListNode extends Node
{
    /**
     * @var Node[]
     */
    private $arguments = [];
    private $placeholderCount = 0;

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
        if ($this->placeholderCount > 0) {
            throw new ParseException('Placeholder arguments must be at the end of the argument list');
        }
        $this->arguments[] = $node;
    }

    public function getChildren() : array
    {
        return $this->arguments;
    }

    public function getCount() : int
    {
        return count($this->arguments) + $this->placeholderCount;
    }

    public function getPlaceholderCount() : int
    {
        return $this->placeholderCount;
    }

    public function addPlaceholderArgument()
    {
        $this->placeholderCount++;
    }

    public function append(ArgumentListNode $args)
    {
        foreach ($args->getChildren() as $arg) {
            $this->add($arg);
        }
        $this->placeholderCount += $args->placeholderCount;
    }
}
