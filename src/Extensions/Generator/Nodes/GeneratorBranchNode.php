<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\EvaluationContext;
use Expresso\Extensions\Generator\Iterators\WrappingIterator;

/**
 * GeneratorBranchNode represents a list comprehension branch.
 *
 * @see GeneratorNode
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
     */
    public function compile(Compiler $compiler)
    {
        $iteratorVariable = $compiler->addTempVariable('new ' . WrappingIterator::class);

        foreach ($this->arguments as $argument) {
            $compiledArgumentNode = (yield $compiler->compileNode($argument));
            $argVarName           = $compiler->addContextAsTempVariable($compiledArgumentNode);

            $argumentName = $argument->getArgumentName();
            $iterator     = "is_array({$argVarName}) ? new \\ArrayIterator({$argVarName}) : {$argVarName}";
            $compiler->addTempVariable("{$iteratorVariable}->addIterator(({$iterator}), '{$argumentName}')");
        }

        if (count($this->filters) > 0) {
            $compiler->pushContext();
            $compiler->add('function(array $arguments) use ($context) {')
                     ->add('$context = $context->createInnerScope($arguments);');
            $filterVars = [];
            foreach ($this->filters as $filter) {
                $filterVars[] = (yield $compiler->compileNode($filter));
            }

            $compiler->compileTempVariables();
            foreach ($filterVars as $filter) {
                $compiler->add("if(!({$filter->source})) {return false;} else \n");
            }
            $compiler->add('return true;}');
            $callbackContext = $compiler->popContext();

            $iteratorVariable = "new \\CallbackFilterIterator({$iteratorVariable}, {$callbackContext->source})";
        }

        $compiler->add($iteratorVariable);
    }

    /**
     * @inheritdoc
     */
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
