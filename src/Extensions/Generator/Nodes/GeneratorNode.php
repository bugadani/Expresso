<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

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
            $iteratorVariable = $branchVariables[0];
        } else {
            $iteratorVariable = $compiler->addTempVariable('new \MultipleIterator();');
            $transformVarName = $compiler->addTempVariable(
                "function(\$arguments) use({$transformVarName}) {
                    \$generatorArguments = [];
                    foreach (\$arguments as \$branchArguments) {
                        \$generatorArguments += \$branchArguments;
                    }
                    return {$transformVarName}(\$generatorArguments);
                }"
            );

            $compiler->compileTempVariables();
            foreach ($branchVariables as $branchVarName) {
                $compiler->add("{$iteratorVariable}->attachIterator({$branchVarName});");
            }
        }

        $compiler->add("foreach ({$iteratorVariable} as \$element) {yield {$transformVarName}(\$element);}");
        $compiler->add('}');

        $context = $compiler->popContext();

        $varName = $compiler->addContextAsTempVariable($context);

        $compiler->add("{$varName}()");
    }

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
                    $mergedArguments = [];
                    foreach ($arguments as $branchArguments) {
                        $mergedArguments += $branchArguments;
                    }

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
