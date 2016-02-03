<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

/**
 * GeneratorNode represents a list comprehension expression. It consists of an expression and a number of branches.
 * Branches are run in parallel and may define a set of arguments as an input of the expression. The argument
 * definitions in a single branch are run in series (i.e. iterated in a nested way) and may be filtered by guard
 * expressions.
 *
 * @package Expresso\Extensions\Generator\Nodes
 */
class GeneratorNode extends Node
{
    /**
     * @var GeneratorBranchNode[]
     */
    private $branches = [];

    /**
     * @var FunctionDefinitionNode
     */
    private $functionBodyNode;

    /**
     * GeneratorNode constructor.
     *
     * @param Node $functionBody
     */
    public function __construct(Node $functionBody)
    {
        $this->functionBodyNode = $functionBody;
    }

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $compiler->pushContext();

        $compiler->add('function() use($context) {');

        $branchVariables = [];
        foreach ($this->branches as $branch) {
            $compiledBranch    = (yield $compiler->compileNode($branch));
            $branchVariables[] = $compiler->addContextAsTempVariable($compiledBranch);
        }

        $compiledTransform = (yield $compiler->compileNode($this->functionBodyNode));
        $transformVarName  = $compiler->addContextAsTempVariable($compiledTransform);

        if (count($this->branches) === 1) {
            $compiler->compileTempVariables();
            $compiler->add(
                "foreach ({$branchVariables[0]} as \$element) {
                    yield {$transformVarName}(\$element);
                }"
            );
        } else {
            $iteratorVariable = $compiler->addTempVariable('new \MultipleIterator()');

            $compiler->compileTempVariables();
            foreach ($branchVariables as $branchVarName) {
                $compiler->add("{$iteratorVariable}->attachIterator({$branchVarName});");
            }

            $compiler->add(
                "foreach ({$iteratorVariable} as \$element) {
                    \$arguments = call_user_func_array('array_merge', \$element);
                    yield {$transformVarName}(\$arguments);
                }"
            );
        }
        $compiler->add('}');

        $context = $compiler->popContext();

        $varName = $compiler->addContextAsTempVariable($context);

        $compiler->add("{$varName}()");
    }

    /**
     * @inheritdoc
     */
    public function evaluate(EvaluationContext $context)
    {
        $transformFunction = (yield $this->functionBodyNode->evaluate($context));

        if (count($this->branches) === 1) {

            $iterator = (yield $this->branches[0]->evaluate($context));

            $generator = function ($iterator) use ($transformFunction) {
                foreach ($iterator as $arguments) {
                    yield $transformFunction($arguments);
                }
            };

        } else {

            $iterator = new \MultipleIterator();
            foreach ($this->branches as $branch) {
                $iterator->attachIterator(yield $branch->evaluate($context));
            }

            $generator = function ($iterator) use ($transformFunction) {
                foreach ($iterator as $arguments) {
                    $mergedArguments = call_user_func_array('array_merge', $arguments);

                    yield $transformFunction($mergedArguments);
                }
            };
        }

        yield new \IteratorIterator($generator($iterator));
    }

    /**
     * @param GeneratorBranchNode $branch
     */
    public function addBranch(GeneratorBranchNode $branch)
    {
        $this->branches[] = $branch;
    }
}
