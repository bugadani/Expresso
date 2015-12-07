<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Operators\Binary\SimpleAccessOperator;
use Expresso\EvaluationContext;

class FunctionCallNode extends Node
{
    /**
     * @var Node
     */
    private $functionName;

    /**
     * @var Node[]
     */
    private $arguments;

    public function __construct($functionName, array $arguments = [])
    {
        $this->functionName = $functionName;
        $this->arguments    = $arguments;
    }

    public function addArgument(Node $node)
    {
        $this->arguments[] = $node;
    }

    public function compile(Compiler $compiler)
    {
        if ($this->functionName instanceof IdentifierNode) {
            /** @var IdentifierNode $functionName */
            $functionName = $this->functionName;

            $compiler->compileExtensionFunction($functionName->getName(), $this->arguments);
        } else {
            if ($this->isSimpleAccessOperator()) {
                $object = $this->functionName->getLeft();
                $method = $this->functionName->getRight();
                $compiler->compileNode($object)
                         ->add('->')
                         ->compileFunction(
                             $method->getName(),
                             $this->arguments
                         );
            }
        }
    }

    public function evaluate(EvaluationContext $context)
    {
        $arguments = array_map(
            function (Node $nodeInterface) use ($context) {
                return $nodeInterface->evaluate($context);
            },
            $this->arguments
        );

        if ($this->functionName instanceof IdentifierNode) {
            /** @var IdentifierNode $functionName */
            $functionName = $this->functionName;

            $callback = $context->getFunction($functionName->getName())->getFunctionName();

            return call_user_func_array($callback, $arguments);
        } else {
            if ($this->isSimpleAccessOperator()) {
                $object     = $this->functionName->getLeft()->evaluate($context);
                $methodName = $this->functionName->getRight()->getName();

                return call_user_func_array([$object, $methodName], $arguments);
            }
        }
    }

    /**
     * @return bool
     */
    private function isSimpleAccessOperator()
    {
        return $this->functionName instanceof BinaryOperatorNode &&
               $this->functionName->isOperator(SimpleAccessOperator::class);
    }
}