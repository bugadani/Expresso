<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
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
     * @var array
     */
    private $argumentNames = [];

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

        foreach ($this->arguments as $argument) {
            $compiledArgumentNode = (yield $compiler->compileNode($argument));
            $argName              = $argument->getArgumentName();
            $argDef               = $compiler->addContextAsTempVariable($compiledArgumentNode);
            $compiler->compileTempVariables();
            $compiler->add("foreach({$argDef} as \$context['{$argName}']) {");
        }

        $filterVars = [];
        foreach ($this->filters as $filter) {
            $filterVars[] = (yield $compiler->compileNode($filter));
        }
        $compiler->compileTempVariables();

        if (count($this->filters) > 0) {
            $compiler->add('$accepted = true;');
            foreach ($filterVars as $filter) {
                $compiler->add("\$accepted = \$accepted && ({$filter->source});");
            }

            $compiler->add('if($accepted) {');
        }

        $compiler->add('yield [');
        foreach ($this->arguments as $arg) {
            $argName = $arg->getArgumentName();
            $compiler->add("'{$argName}' => \$context['{$argName}'],");
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
        $compiler->add($compiler->addContextAsTempVariable($callbackContext) . '()');
    }

    /**
     * @inheritdoc
     */
    public function evaluate(EvaluationContext $context)
    {
        //Holds functions that should initialize argument sources
        $source = [];
        foreach ($this->arguments as $argument) {
            $source[ $argument->getArgumentName() ] = function ($context) use ($argument) {
                $value = GeneratorHelper::executeGeneratorsRecursive($argument->evaluate($context));
                if (is_array($value)) {
                    $value = new \ArrayIterator($value);
                }

                $value->rewind();

                return $value;
            };
        }

        $iterationContext = $context->createInnerScope([]);
        $argumentSource   = function () use ($iterationContext, $source) {

            //reset
            $iteratorList     = new \SplStack();
            $iterators        = new \SplObjectStorage();
            $iteratorsToReset = new \SplDoublyLinkedList();

            $iteratorsToReset->setIteratorMode(\SplDoublyLinkedList::IT_MODE_DELETE);

            $add = function ($iterator, $argName) use ($iterators, $iteratorList, $iterationContext) {
                $iterators[ $iterator ] = $argName;
                $iteratorList->push($iterator);
            };

            $create = function ($iteratorSource) use ($iterationContext) {
                $iterator = $iteratorSource($iterationContext);
                $iterator->rewind();

                return $iterator;
            };

            $createA = function ($argName) use ($source, $create, $add, $iterationContext) {
                $iteratorSource = $source[ $argName ];
                $iterator       = $create($iteratorSource);
                $add($iterator, $argName);

                $iterationContext[ $argName ] = $iterator->current();
            };

            foreach ($source as $argName => $iteratorSource) {
                $createA($argName);
            }

            //Stack-like iteration mode so that rewind goes to the end of the list
            $iteratorList->rewind();

            //valid
            while ($iteratorList->bottom()->valid()) {

                //current
                $values = [];
                foreach ($iterators as $iterator) {
                    $key            = $iterators[ $iterator ];
                    $value          = $iterator->current();
                    $values[ $key ] = $value;

                    $iterationContext[ $key ] = $value;
                }

                yield $values;

                //next

                $currentIterator = $iteratorList->top();
                $currentIterator->next();

                if ($currentIterator->valid()) {
                    $argName                      = $iterators[ $currentIterator ];
                    $iterationContext[ $argName ] = $currentIterator->current();
                }
                //wrap-around
                while (!$currentIterator->valid()) {
                    $iteratorsToReset->push($iteratorList->pop());

                    if ($iteratorList->isEmpty()) {
                        break;
                    }

                    $currentIterator = $iteratorList->top();
                    $currentIterator->next();

                    if ($currentIterator->valid()) {
                        $argName                      = $iterators[ $currentIterator ];
                        $iterationContext[ $argName ] = $currentIterator->current();
                    }
                }

                if ($iteratorList->isEmpty()) {
                    break;
                }

                foreach ($iteratorsToReset as $iter) {
                    $argName = $iterators[ $iter ];
                    $iterators->detach($iter);

                    $createA($argName);
                }
                $iteratorList->rewind();
            }
        };

        $iterator = $argumentSource();

        if (count($this->filters) > 0) {
            $callback = function ($x) use ($context) {
                $context = $context->createInnerScope($x);
                foreach ($this->filters as $filter) {
                    if (!GeneratorHelper::executeGeneratorsRecursive($filter->evaluate($context))) {
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
        $this->argumentNames[] = $argumentNode->getArgumentName();
        $this->arguments[]     = $argumentNode;
    }

    /**
     * @param Node $filterNode
     */
    public function addFilter(Node $filterNode)
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
