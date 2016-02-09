<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerContext;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
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
     * @var GeneratorArgumentNode[]
     */
    private $arguments = [];

    /**
     * @var Node[]
     */
    private $filters = [];

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

        if (count($this->filters) > 0) {
            $compiler->add('$accepted = true;');
            foreach ($this->filters as $filter) {
                $filter = (yield $compiler->compileNode($filter));
                $compiler->add("\$accepted = \$accepted && ({$filter});");
            }

            $compiler->add('if($accepted) {');
        }

        $compiler->add('yield [');
        foreach ($this->arguments as $argName => $arg) {
            $compiler->add("'{$argName}' => ")
                     ->addVariableAccess($argName)
                     ->add(',');
        }
        $compiler->add('];');

        if (count($this->filters) > 0) {
            $compiler->add('}');
        }
        for ($i = 0; $i < count($this->arguments); $i++) {
            $compiler->add("}");
        }
        $compiler->add("}");

        $callbackContext = $compiler->popContext();
        $compiler->add($compiler->addTempVariable($callbackContext) . '()');
    }

    /**
     * @inheritdoc
     */
    public function evaluate(EvaluationContext $context)
    {
        $iterationContext = $context->createInnerScope([]);
        $argumentSource   = function ($source) use ($iterationContext) {
            //reset
            $iteratorList     = new \SplStack();
            $iterators        = new \SplObjectStorage();
            $iteratorsToReset = new \SplDoublyLinkedList();

            $iteratorsToReset->setIteratorMode(\SplDoublyLinkedList::IT_MODE_DELETE);

            $recreateIterator = function ($argName, $constructor) use ($iteratorList, $iterators, $iterationContext) {
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

            $callback = function () use ($iterationContext, $generators) {
                foreach ($generators as $generator) {
                    if (!$generator($iterationContext)) {
                        return false;
                    }
                }

                return true;
            };
            $iterator = new \CallbackFilterIterator($iterator, $callback);
        } else {
            $iterator = new \IteratorIterator($iterator);
        }
        yield $iterator;
    }

    /**
     * @param GeneratorArgumentNode $argumentNode
     */
    public function addArgument(GeneratorArgumentNode $argumentNode)
    {
        $argumentName = $argumentNode->getArgumentName();

        $this->arguments[ $argumentName ] = $argumentNode;
    }

    /**
     * @param Node $filterNode
     */
    public function addFilter(Node $filterNode)
    {
        $this->filters[] = $filterNode;
    }
}
