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

        $iterators = [];
        foreach ($this->arguments as $argument) {
            $compiledArgumentNode                      = (yield $compiler->compileNode($argument));
            $iterators[ $argument->getArgumentName() ] = $compiler->addContextAsTempVariable($compiledArgumentNode);
        }
        $compiler->compileTempVariables();

        foreach ($iterators as $argName => $argDef) {
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
        foreach ($iterators as $argName => $argDef) {
            $compiler->add("'{$argName}' => \$context['{$argName}'],");
        }
        $compiler->add('];');
        if (count($this->filters) > 0) {
            $compiler->add('}');
        }
        for ($i = 0; $i < count($iterators); $i++) {
            $compiler->add("}");
        }
        $compiler->add("}");

        $callbackContext = $compiler->popContext();
        $compiler->add($compiler->addContextAsTempVariable($callbackContext).'()');
    }

    /**
     * @inheritdoc
     */
    public function evaluate(EvaluationContext $context)
    {
        $iterator = new WrappingIterator();

        //TODO
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
