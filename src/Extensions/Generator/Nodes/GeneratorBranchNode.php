<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerContext;
use Expresso\Compiler\Node;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\EvaluationContext;

/**
 * GeneratorBranchNode represents a list comprehension branch.
 *
 * @see     GeneratorNode
 *
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

            $compiler->add("foreach({$compiledArgumentNode} as \$context['{$argName}']) {");
        }

        $filterVars = [];
        foreach ($this->filters as $filter) {
            $filterVars[] = (yield $compiler->compileNode($filter));
        }

        if (count($this->filters) > 0) {
            $compiler->add('$accepted = true;');
            foreach ($filterVars as $filter) {
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

            $recreateIterator = function ($argName) use ($source, $iteratorList, $iterators, $iterationContext) {
                $iterator = $source[ $argName ]($iterationContext);

                $iterators->attach($iterator, $argName);
                $iteratorList->push($iterator);

                $iterationContext[ $argName ] = $iterator->current();
            };

            $updateValues = function (\Iterator $iterator) use ($iterationContext, $iterators) {
                if ($iterator->valid()) {
                    $argName                      = $iterators[ $iterator ];
                    $iterationContext[ $argName ] = $iterator->current();
                }
            };

            foreach ($source as $argName => $iteratorSource) {
                $recreateIterator($argName);
            }

            //valid
            while ($iteratorList->bottom()->valid()) {

                //current
                yield $iterationContext->getArrayCopy();

                //next
                $currentIterator = $iteratorList->top();
                $currentIterator->next();

                $updateValues($currentIterator);
                //wrap-around
                while (!$currentIterator->valid()) {
                    $iteratorsToReset->push($iteratorList->pop());

                    if ($iteratorList->isEmpty()) {
                        break;
                    }

                    $currentIterator = $iteratorList->top();
                    $currentIterator->next();

                    $updateValues($currentIterator);
                }

                if ($iteratorList->isEmpty()) {
                    break;
                }

                foreach ($iteratorsToReset as $iteratorToReset) {
                    $recreateIterator($iterators[ $iteratorToReset ]);
                    $iterators->detach($iteratorToReset);
                }
            }
        };

        //Holds functions that should initialize argument sources
        $source = [];
        foreach ($this->arguments as $argumentName => $argument) {
            $source[ $argumentName ] = function ($context) use ($argument) {
                $value = \Expresso\runQuasiRecursive($argument->evaluate($context));
                if (is_array($value)) {
                    $value = new \ArrayIterator($value);
                }

                $value->rewind();

                return $value;
            };
        }

        $iterator = $argumentSource($source);

        if (count($this->filters) > 0) {
            $callback = function () use ($iterationContext) {
                foreach ($this->filters as $filter) {
                    if (!\Expresso\runQuasiRecursive($filter->evaluate($iterationContext))) {
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
