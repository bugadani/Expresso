<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\EvaluationContext;

class FunctionCallNode extends Node
{
    /**
     * @var NodeInterface
     */
    private $functionName;

    /**
     * @var NodeInterface[]
     */
    private $arguments;

    public function __construct($functionName)
    {
        $this->functionName = $functionName;
        $this->arguments    = [];
    }

    public function addArgument(NodeInterface $node)
    {
        $this->arguments[] = $node;
    }

    public function compile(Compiler $compiler)
    {
        /** @var IdentifierNode $functionName */
        $functionName = $compiler->getConfiguration()
                                 ->getFunctions()[ $this->functionName->getName() ]
            ->getFunctionName();
        $compiler->compileFunction($functionName, $this->arguments);
    }

    public function evaluate(EvaluationContext $context)
    {
        $arguments = array_map(
            function (NodeInterface $nodeInterface) use ($context) {
                return $nodeInterface->evaluate($context);
            },
            $this->arguments
        );

        /** @var IdentifierNode $functionName */
        $functionName = $this->functionName;

        return $context->getFunction($functionName->getName())->call($arguments);
    }
}