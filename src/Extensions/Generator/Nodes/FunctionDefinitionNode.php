<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\DataNode;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\EvaluationContext;
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
     * @var bool
     */
    private $isSimpleNode;

    /**
     * FunctionDefinitionNode constructor.
     *
     * @param Node $functionBody
     */
    public function __construct(Node $functionBody)
    {
        $this->functionBody = $functionBody;
        $this->isSimpleNode = $functionBody instanceof IdentifierNode || $functionBody instanceof DataNode;
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
    public function evaluate(EvaluationContext $context)
    {
        if ($this->isSimpleNode) {
            yield function (array $arguments) use ($context) {
                $innerContext = $context->createInnerScope($arguments);

                return $this->functionBody->evaluate($innerContext)->current();
            };
        } else {
            yield function (array $arguments) use ($context) {
                $innerContext = $context->createInnerScope($arguments);

                $function = new Recursor([$this->functionBody, 'evaluate']);

                return $function($innerContext);
            };
        }
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return [$this->functionBody];
    }
}
