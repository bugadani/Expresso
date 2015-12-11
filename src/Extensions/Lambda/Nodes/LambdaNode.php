<?php

namespace Expresso\Extensions\Lambda\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
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
        $functionBody->addData('noEvaluate');
        $this->arguments = $arguments;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('function(');

        $argumentCount = count($this->arguments);
        if ($argumentCount > 0) {
            $compiler->add('$' . implode(', $', $this->arguments));
        }

        $compiler->add(') use ($context) {')
                 ->add('$context = new Expresso\\ExecutionContext([');

        if ($argumentCount > 0) {
            $argumentNames    = $this->arguments;
            $lastArgumentName = array_pop($argumentNames);
            foreach ($argumentNames as $argName) {
                $compiler->compileString($argName)
                         ->add(" => \${$argName}, ");
            }
            $compiler->compileString($lastArgumentName)
                     ->add(" => \${$lastArgumentName}, ");
        }

        $compiler->add('], $context);')
                 ->add('return ')
                 ->compileNode($this->getChildAt(0))
                 ->add(';}');
    }

    public function evaluate(EvaluationContext $context, array $childResults)
    {
        return function () use ($context) {
            $evaluator    = new NodeTreeEvaluator();
            $arguments    = array_slice(func_get_args(), 0, count($this->arguments));
            $innerContext = $context->createInnerScope(array_combine($this->arguments, $arguments));

            $functionBody = $this->getChildAt(0);

            $functionBody->removeData('noEvaluate');
            $result = $evaluator->evaluate($functionBody, $innerContext);
            $functionBody->addData('noEvaluate');

            return $result;
        };
    }
}