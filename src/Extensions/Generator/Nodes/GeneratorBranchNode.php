<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerContext;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;
use Recursor\Recursor;

/**
 * GeneratorBranchNode represents a list comprehension branch.
 *
 * @see     GeneratorNode
 * @package Expresso\Extensions\Generator\Nodes
 */
class GeneratorBranchNode extends Node
{
    /**
     * @var Node[]
     */
    private $arguments = [];

    /**
     * @var Node[]
     */
    private $filters = [];

    /**
     * @param string $argumentName
     * @param Node $argumentNode
     */
    public function addArgument($argumentName, Node $argumentNode)
    {
        $this->arguments[ $argumentName ] = $argumentNode;
    }

    /**
     * @param Node $filterNode
     */
    public function addFilter(Node $filterNode)
    {
        $this->filters[] = $filterNode;
    }

    /**
     * @param Compiler $compiler
     *
     * @return \Generator
     */
    public function compile(Compiler $compiler)
    {
        $compiler->pushContext();
        $compiler->add('function() use ($context) {')
                 ->add('$context = $context->createInnerScope([]);');

        foreach ($this->arguments as $argName => $argument) {
            /** @var CompilerContext $compiledArgumentNode */
            $compiledArgumentNode = (yield $compiler->compileNode($argument));

            $compiler->add("foreach({$compiledArgumentNode} as ")
                     ->addVariableAccess($argName)
                     ->add(') {');
        }

        $hasFilters = count($this->filters) > 0;
        if ($hasFilters) {
            $compiledFilters = [];
            foreach ($this->filters as $filter) {
                $compiledFilters[] = (yield $compiler->compileNode($filter));
            }
            $compiler->add('if ((')
                     ->add(implode(') && (', $compiledFilters))
                     ->add(')) {');
        }

        $compiler->add('yield [');
        foreach ($this->arguments as $argName => $arg) {
            $compiler->add("'{$argName}' => ")
                     ->addVariableAccess($argName)
                     ->add(',');
        }
        $compiler->add('];');

        if ($hasFilters) {
            $compiler->add('}');
        }
        $compiler->add(str_repeat("}", count($this->arguments) + 1));

        $callbackContext = $compiler->popContext();
        $compiler->add($compiler->addTempVariable($callbackContext) . '()');
    }

    /**
     * @inheritdoc
     */
    public function evaluate(ExecutionContext $context)
    {
        $iterationContext = $context->createInnerScope([]);
        $argumentSource   = function ($source) use ($iterationContext) {
            //reset
            $iteratorList     = new \SplStack();
            $iterators        = new \SplObjectStorage();
            $iteratorsToReset = new \SplDoublyLinkedList();

            $iteratorsToReset->setIteratorMode(\SplDoublyLinkedList::IT_MODE_DELETE);

            $recreateIterator = function ($argName, $constructor) use ($iteratorList, $iterators, $iterationContext) {
                /** @var \Iterator $iterator */
                $iterator = $constructor($iterationContext);

                $iterators->attach($iterator, $argName);
                $iteratorList->push($iterator);

                $iterationContext[ $argName ] = $iterator->current();
            };

            foreach ($source as $argName => $iteratorSource) {
                $recreateIterator($argName, $iteratorSource);
            }

            //valid
            while (true) {

                //current
                yield $iterationContext->getArrayCopy();

                //next: mark finished iterators to be reset, step the first valid
                do {
                    //step the next iterator that is still valid
                    $currentIterator = $iteratorList->top();
                    $currentIterator->next();

                    $currentIteratorValid = $currentIterator->valid();

                    if (!$currentIteratorValid) {
                        //wrap-around: push the current iterator to the "to reset" list
                        $iteratorsToReset->push($iteratorList->pop());
                    }
                } while (!$currentIteratorValid && !$iteratorList->isEmpty());

                if ($iteratorList->isEmpty()) {
                    break;
                }

                //update the value in context
                $argName                      = $iterators[ $currentIterator ];
                $iterationContext[ $argName ] = $currentIterator->current();

                foreach ($iteratorsToReset as $iteratorToReset) {
                    $argName = $iterators[ $iteratorToReset ];
                    $recreateIterator($argName, $source[ $argName ]);
                    $iterators->detach($iteratorToReset);
                }
            }
        };

        //Holds functions that should initialize argument sources
        $source = [];
        foreach ($this->arguments as $argumentName => $argument) {
            $generator = new Recursor([$argument, 'evaluate']);

            $source[ $argumentName ] = function ($context) use ($generator) {
                $value = $generator($context);
                if (is_array($value)) {
                    $value = new \ArrayIterator($value);
                }

                $value->rewind();

                return $value;
            };
        }

        $iterator = $argumentSource($source);

        if (count($this->filters) > 0) {

            $generators = [];
            foreach ($this->filters as $filter) {
                $generators[] = new Recursor([$filter, 'evaluate']);
            }

            $filterBranchCallback = function () use ($iterationContext, $generators) {
                foreach ($generators as $generator) {
                    if (!$generator($iterationContext)) {
                        return false;
                    }
                }

                return true;
            };

            $iterator = new \CallbackFilterIterator($iterator, $filterBranchCallback);
        } else {
            $iterator = new \IteratorIterator($iterator);
        }

        return $iterator;
    }
}
