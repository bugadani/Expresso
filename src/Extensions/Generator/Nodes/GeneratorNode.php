<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\ExecutionContext;

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
     * @param GeneratorBranchNode $branch
     */
    public function addBranch(GeneratorBranchNode $branch)
    {
        $this->branches[] = $branch;
    }

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $compiler->pushContext();

        $compiler->add('function() use ($context) {');

        $branchVariables = [];
        foreach ($this->branches as $branch) {
            $branchVariables[] = (yield $compiler->compileNode($branch));
        }

        //Function bodies can't be inlined
        $transformVarName = (yield $compiler->compileNode($this->functionBodyNode, false));

        if (count($this->branches) === 1) {
            $compiler->compileStatements();
            $compiler->add(
                "foreach ({$branchVariables[0]} as \$element) {
                    yield {$transformVarName}(\$element);
                }"
            );
        } else {
            $iteratorVariable = $compiler->addTempVariable('new \MultipleIterator()');
            $compiler->compileStatements();
            foreach ($branchVariables as $branchVarName) {
                $compiler->add("{$iteratorVariable}->attachIterator({$branchVarName});");
            }

            $compiler->add(
                "foreach ({$iteratorVariable} as \$element) {
                    \$arguments = \\array_merge(...\$element);
                    yield {$transformVarName}(\$arguments);
                }"
            );
        }
        $compiler->add('}');

        $context = $compiler->popContext();
        $compiler->add($compiler->addTempVariable($context) . '()');
    }

    /**
     * @inheritdoc
     */
    public function evaluate(ExecutionContext $context)
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
                    $mergedArguments = array_merge(...$arguments);

                    yield $transformFunction($mergedArguments);
                }
            };
        }

        return $generator($iterator);
    }
}
