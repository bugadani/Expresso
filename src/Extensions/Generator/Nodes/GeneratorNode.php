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
        /* $branches = [];

         foreach ($this->branches as $branch) {
             $branches[] = (yield $compiler->compile($branch));
         }
 */

    }

    public function evaluate(EvaluationContext $context)
    {
        if (count($this->branches) === 1) {
            $branch   = reset($this->branches);
            $iterator = (yield $branch->evaluate($context));

            $createContext = [$context, 'createInnerScope'];

        } else {

            $iterator = new \MultipleIterator();
            foreach ($this->branches as $branch) {
                $iterator->attachIterator(yield $branch->evaluate($context));
            }

            $createContext = function ($arguments) use($context) {
                $generatorArguments = [];
                foreach ($arguments as $branchArguments) {
                    $generatorArguments += $branchArguments;
                }

                return $context->createInnerScope($generatorArguments);
            };
        }

        $generator = function ($iterator) use ($createContext) {
            foreach ($iterator as $arguments) {
                yield GeneratorHelper::executeGeneratorsRecursive(
                    $this->functionBody->evaluate($createContext($arguments))
                );
            }
        };
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