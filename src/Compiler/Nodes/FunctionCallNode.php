<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\Binary\ArrayAccessOperator;
use Expresso\Compiler\Operators\Binary\SimpleAccessOperator;
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
        if ($this->functionName instanceof IdentifierNode) {
            /** @var IdentifierNode $functionName */
            $functionName = $compiler->getConfiguration()
                                     ->getFunctions()[ $this->functionName->getName() ]
                ->getFunctionName();
            $compiler->compileFunction($functionName, $this->arguments);
        } else {
            if ($this->functionName instanceof BinaryOperatorNode && $this->functionName->getOperator() instanceof SimpleAccessOperator) {
                $compiler->compileNode($this->functionName->getLeft())
                         ->add('->')
                         ->compileFunction($this->functionName->getRight()->getName(), $this->arguments);
            }
        }
    }

    public function evaluate(EvaluationContext $context)
    {
        $arguments = array_map(
            function (NodeInterface $nodeInterface) use ($context) {
                return $nodeInterface->evaluate($context);
            },
            $this->arguments
        );

        if ($this->functionName instanceof IdentifierNode) {
            /** @var IdentifierNode $functionName */
            $functionName = $this->functionName;

            return $context->getFunction($functionName->getName())->call($arguments);
        } else {
            if ($this->functionName instanceof BinaryOperatorNode && $this->functionName->getOperator() instanceof SimpleAccessOperator) {
                $object = $this->functionName->getLeft()->evaluate($context);
                $methodName = $this->functionName->getRight()->getName();

                return call_user_func_array([$object, $methodName], $arguments);
            }
        }
    }
}