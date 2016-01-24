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
    private $arguments     = [];
    private $filters       = [];
    private $argumentNames = [];

    public function compile(Compiler $compiler)
    {
        // TODO: Implement compile() method.
    }

    public function evaluate(EvaluationContext $context)
    {
        $iterator = new WrappingIterator();
        if (count($this->arguments) > 1) {
            foreach ($this->arguments as $argument) {
                $iterator->addIterator(yield $argument->evaluate($context), $argument->getArgumentName());
            }
        } else {
            $value = (yield $this->arguments[0]->evaluate($context));
            if (is_array($value)) {
                $value = new \ArrayIterator($value);
            }
            $iterator->addIterator($value);
        }

        //TODO: a filter should receive all arguments defined in current branch
        //as well as the outer context
        $filterCount = count($this->filters);
        switch ($filterCount) {
            case 0:
                break;
            case 1:
                $iterator = new \CallbackFilterIterator(
                    $iterator,
                    yield $this->filters[0]
                );
                break;
            default:
                $filters = [];
                foreach ($this->filters as $filter) {
                    $filters[] = (yield ($filter->evaluate($context)));
                }

                $iterator = new \CallbackFilterIterator(
                    $iterator,
                    function ($x) use ($filters) {
                        foreach ($filters as $filter) {
                            if (!$filter($x)) {
                                return false;
                            }
                        }

                        return true;
                    }
                );
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