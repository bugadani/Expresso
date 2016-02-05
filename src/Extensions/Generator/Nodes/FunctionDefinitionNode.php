<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Utils\GeneratorHelper;
use Expresso\EvaluationContext;

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

                return GeneratorHelper::executeGeneratorsRecursive(
                    $this->functionBody->evaluate($innerContext)
                );
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
