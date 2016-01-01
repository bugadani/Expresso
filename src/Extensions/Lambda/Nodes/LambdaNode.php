<?php

namespace Expresso\Extensions\Lambda\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\EvaluationContext;

class LambdaNode extends Node
{
    /**
     * @var string[]
     */
    private $arguments;

    public function __construct(Node $functionBody, array $arguments)
    {
        $this->addChild($functionBody);
        $this->arguments = $arguments;
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

        $compiler->add(']);')
                 ->add('return ');
        yield $compiler->compileNode($this->getChildAt(0));
        $compiler->add(';}');
    }

    public function evaluate(EvaluationContext $context)
    {
        $context->setReturnValue(
            function () use ($context) {
                $arguments    = array_slice(func_get_args(), 0, count($this->arguments));
                $innerContext = $context->createInnerScope(array_combine($this->arguments, $arguments));

                $functionBody = $this->getChildAt(0);
                GeneratorHelper::executeGeneratorsRecursive(
                    $functionBody->evaluate($innerContext),
                    [$innerContext, 'getReturnValue']
                );

                return $innerContext->getReturnValue();
            }
        );
        yield;
    }
}