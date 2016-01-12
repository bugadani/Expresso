<?php

namespace Expresso\Extensions\Lambda\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\EvaluationContext;

class LambdaNode extends Node
{
    /**
     * @var Node
     */
    private $functionBody;

    /**
     * @var string[]
     */
    private $arguments;

    public function __construct(Node $functionBody, array $arguments)
    {
        $this->arguments    = $arguments;
        $this->functionBody = $functionBody;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('function(');

        $hasArguments = count($this->arguments) > 0;
        if ($hasArguments) {
            $compiler->add('$' . implode(', $', $this->arguments));
        }

        $compiler->add(') use ($context) {')
                 ->add('$context = $context->createInnerScope([');

        if ($hasArguments) {
            $argumentNames    = $this->arguments;
            $lastArgumentName = array_pop($argumentNames);
            foreach ($argumentNames as $argName) {
                $compiler->compileString($argName)
                         ->add(" => \${$argName}, ");
            }
            $compiler->compileString($lastArgumentName)
                     ->add(" => \${$lastArgumentName}");
        }

        $compiledFunctionBody = (yield $compiler->compileNode($this->functionBody));

        $compiler->add(']);');
        $compiler->compileTempVariables();
        $compiler->add('return ');
        $compiler->add($compiledFunctionBody->source);
        $compiler->add(';}');
    }

    public function evaluate(EvaluationContext $context)
    {
        yield function () use ($context) {
            $arguments    = array_slice(func_get_args(), 0, count($this->arguments));
            $innerContext = $context->createInnerScope(array_combine($this->arguments, $arguments));

            return GeneratorHelper::executeGeneratorsRecursive(
                $this->functionBody->evaluate($innerContext)
            );
        };
    }

    public function getChildren()
    {
        return [$this->functionBody];
    }
}