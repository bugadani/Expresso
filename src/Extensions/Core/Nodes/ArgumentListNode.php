<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;
use Expresso\Runtime\PlaceholderArgument;

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
            for ($i = 0; $i < $this->getCount(); $i++) {
                if (!isset($this->arguments[ $i ]) || $this->arguments[ $i ] instanceof PlaceholderArgument) {
                    $placeholderClass = PlaceholderArgument::class;
                    $compiler->add("new {$placeholderClass}");
                } else {
                    $compiler->add(yield $compiler->compileNode($this->arguments[ $i ]));
                }
                if ($i != $this->getCount() - 1) {
                    $compiler->add(', ');
                }
            }
        }
    }

    public function evaluate(ExecutionContext $context)
    {
        $list = [];
        foreach ($this->arguments as $k => $child) {
            $list[ $k ] = (yield $child->evaluate($context));
        }

        return $list;
    }

    public function add(Node $node)
    {
        $this->arguments[ $this->getCount() ] = $node;
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
        $keyOffset = $this->getCount();
        foreach ($args->arguments as $key => $arg) {
            $this->arguments[ $keyOffset + $key ] = $arg;
        }
        $this->placeholderCount += $args->placeholderCount;
    }
}
