<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;
use Recursor\Recursor;

/**
 * FunctionDefinitionNode provides a function that encapsulates a given expression.
 *
 * @package Expresso\Extensions\Generator\Nodes
 */
class FunctionDefinitionNode extends Node
{
    /**
     * @var Node
     */
    private $functionBody;

    /**
     * FunctionDefinitionNode constructor.
     *
     * @param Node $functionBody
     */
    public function __construct(Node $functionBody)
    {
        $this->functionBody = $functionBody;
    }

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $compiler->add('function(array $arguments) use ($context) {')
                 ->add('$context = $context->createInnerScope($arguments);');

        $compiledFunctionBody = (yield $compiler->compileNode($this->functionBody));

        $compiler->compileStatements();
        $compiler->add("return {$compiledFunctionBody};");

        $compiler->add("}");
    }

    /**
     * @inheritdoc
     */
    public function evaluate(ExecutionContext $context)
    {
        $function = new Recursor([$this->functionBody, 'evaluate']);
        return function (array $arguments) use ($context, $function) {
            $innerContext = $context->createInnerScope($arguments);

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
