<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
use Expresso\Extensions\Generator\Iterators\WrappingIterator;

class GeneratorBranchNode extends Node
{
    /**
     * @var GeneratorArgumentNode[]
     */
    private $arguments = [];

    /**
     * @var Node[]
     */
    private $filters       = [];
    private $argumentNames = [];

    public function compile(Compiler $compiler)
    {
        // TODO: Implement compile() method.
    }

    public function evaluate(EvaluationContext $context)
    {
        $iterator = new WrappingIterator();

        foreach ($this->arguments as $argument) {
            $value = (yield $argument->evaluate($context));
            if (is_array($value)) {
                $value = new \ArrayIterator($value);
            }
            $iterator->addIterator($value, $argument->getArgumentName());
        }

        if (count($this->filters) > 0) {
            $filters = [];
            foreach ($this->filters as $filter) {
                $filters[] = (yield $filter->evaluate($context));
            }

            $callback = function ($x) use ($filters) {
                foreach ($filters as $filter) {
                    if (!$filter($x)) {
                        return false;
                    }
                }

                return true;
            };
            $iterator = new \CallbackFilterIterator($iterator, $callback);
        }
        yield $iterator;
    }

    public function addArgument(GeneratorArgumentNode $argumentNode)
    {
        $this->argumentNames[] = $argumentNode->getArgumentName();
        $this->arguments[]     = $argumentNode;
    }

    public function addFilter(GeneratorFilterNode $filterNode)
    {
        $this->filters[] = $filterNode;
    }

    /**
     * @return array
     */
    public function getArgumentNames()
    {
        return $this->argumentNames;
    }
}