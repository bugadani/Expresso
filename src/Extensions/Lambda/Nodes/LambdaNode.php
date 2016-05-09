<?php

namespace Expresso\Extensions\Lambda\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Nodes\CallableNode;
use Recursor\Recursor;

/**
 * Class LambdaNode represents a lambda expression in the Abstract Syntax Tree.
 *
 * @package Expresso\Extensions\Lambda\Nodes
 */
class LambdaNode extends CallableNode
{
    /**
     * @var Node
     */
    private $functionBody;

    /**
     * @var string[]
     */
    private $arguments;

    /**
     * LambdaNode constructor.
     *
     * @param Node  $functionBody
     * @param array $arguments
     */
    public function __construct(Node $functionBody, array $arguments)
    {
        $this->arguments    = $arguments;
        $this->functionBody = $functionBody;
    }

    /**
     * @inheritdoc
     */
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
        $compiler->add(']);');

        $compiledFunctionBody = (yield $compiler->compileNode($this->functionBody));
        $compiler->compileStatements();

        $compiler->add("return {$compiledFunctionBody};}");
    }

    /**
     * @inheritdoc
     */
    public function evaluate(EvaluationContext $context)
    {
        return function () use ($context) {
            $arguments    = array_slice(func_get_args(), 0, count($this->arguments));
            $innerContext = $context->createInnerScope(array_combine($this->arguments, $arguments));

            $function = new Recursor([$this->functionBody, 'evaluate']);

            return $function($innerContext);
        };
    }

    /**
     * @inheritdoc
     */
    public function getChildren() : array
    {
        return [$this->functionBody];
    }
}