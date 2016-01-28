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

    /**
     * @var array
     */
    private $argumentNames = [];

    public function compile(Compiler $compiler)
    {
        $iteratorVariable = $compiler->addTempVariable('new ' . WrappingIterator::class);

        $arguments = [];
        foreach ($this->arguments as $argument) {
            $compiledArgumentNode                      = (yield $compiler->compileNode($argument));
            $argVarName                                = $compiler->addContextAsTempVariable($compiledArgumentNode);
            $arguments[ $argument->getArgumentName() ] = $argVarName;
        }

        foreach ($arguments as $argumentName => $argVarName) {
            $compiler->addTempVariable(
                "{$iteratorVariable}->addIterator((is_array({$argVarName}) ? new \\ArrayIterator({$argVarName}) : {$argVarName}), '{$argumentName}')"
            );
        }

        switch (count($this->filters)) {
            case 0:
                break;
            case 1:
                $filterVar = $compiler->addContextAsTempVariable(
                    yield $compiler->compileNode(reset($this->filters))
                );

                $iteratorVariable = $compiler->addTempVariable(
                    "new \\CallbackFilterIterator({$iteratorVariable}, {$filterVar})"
                );
                break;
            default:

                $compiler->pushContext();
                $compiler->add('function($value) use($context) {');
                $filterVars = [];
                foreach ($this->filters as $filter) {
                    $filterVars[] = $compiler->addContextAsTempVariable(
                        yield $compiler->compileNode($filter)
                    );
                }
                $compiler->compileTempVariables();
                foreach ($filterVars as $filter) {
                    $compiler->add("if(!{$filter}(\$value)) {return false;} else \n");
                }
                $compiler->add('return true;}');
                $callbackContext = $compiler->popContext();

                $iteratorVariable = $compiler->addTempVariable(
                    "new \\CallbackFilterIterator({$iteratorVariable}, {$callbackContext->source})"
                );
                break;
        }
        $compiler->add($iteratorVariable);
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
