<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\EvaluationContext;

class GeneratorNode extends Node
{
    /**
     * @var GeneratorBranchNode[]
     */
    private $branches = [];

    /**
     * @var Node
     */
    private $functionBody;

    /**
     * GeneratorNode constructor.
     *
     * @param Node $functionBody
     */
    public function __construct(Node $functionBody)
    {
        $this->functionBody = $functionBody;
    }

    public function compile(Compiler $compiler)
    {
        // TODO: Implement compile() method.
    }

    public function evaluate(EvaluationContext $context)
    {
        $iterator = new \MultipleIterator();

        $argumentNames = [];

        foreach ($this->branches as $branch) {
            $argumentNames[] = $branch->getArgumentNames();
            $iterator->attachIterator(yield $branch->evaluate($context));
        }

        $generator = function () use ($context, $iterator, $argumentNames) {
            foreach ($iterator as $arguments) {
                $generatorArguments = [];
                foreach ($arguments as $idx => $branchArguments) {
                    foreach ($argumentNames[ $idx ] as $argumentName) {
                        $generatorArguments[ $argumentName ] = $branchArguments[ $argumentName ];
                    }
                }
                $innerContext = $context->createInnerScope($generatorArguments);

                yield GeneratorHelper::executeGeneratorsRecursive(
                    $this->functionBody->evaluate($innerContext)
                );
            }
        };
        yield new \IteratorIterator($generator());
    }

    /**
     * @param GeneratorBranchNode $branch
     */
    public function addBranch(GeneratorBranchNode $branch)
    {
        $this->branches[] = $branch;
    }
}