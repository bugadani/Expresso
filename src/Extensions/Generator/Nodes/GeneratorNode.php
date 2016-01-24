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
        $argumentNames = $this->branches[0]->getArgumentNames();
        $iterator      = (yield $this->branches[0]->evaluate($context));

        $generator = function () use ($context, $iterator, $argumentNames) {
            foreach ($iterator as $arguments) {
                $args = [];
                foreach ($arguments as $idx => $argument) {
                    $args[ $argumentNames[ $idx ] ] = $argument;
                }
                $innerContext = $context->createInnerScope($args);

                yield GeneratorHelper::executeGeneratorsRecursive($this->functionBody->evaluate($innerContext));
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