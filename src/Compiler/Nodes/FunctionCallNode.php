<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;

class FunctionCallNode extends Node
{

    public function __construct($functionName, array $arguments = [])
    {
        $this->addChild($functionName);
        $this->addChild(new ArgumentListNode($arguments));
    }

    public function addArgument(Node $node)
    {
        $this->getChildAt(1)->addChild($node);
    }

    public function compile(Compiler $compiler)
    {
        $functionName = $this->getChildAt(0);
        if ($functionName instanceof IdentifierNode) {
            $functionName          = $functionName->getName();
            $functions             = $compiler->getConfiguration()->getFunctions();
            $extensionFunctionName = $functions[ $functionName ]->getFunctionName();

            $compiler->add($extensionFunctionName);
        } else {
            if ($this->isSimpleAccessOperator($functionName)) {
                $object = $functionName->getChildAt(0);
                $method = $functionName->getChildAt(1);

                $compiler->compileNode($object)
                         ->add('->')
                         ->add($method->getName());
            }
        }

        $arguments = $this->getChildAt(1);
        $compiler
            ->add('(')
            ->compileNode($arguments)
            ->add(')');
    }

    public function evaluate(EvaluationContext $context)
    {
        $functionName = $this->getChildAt(0);
        if ($functionName instanceof IdentifierNode) {
            $functionName = $functionName->getName();
            $callback     = $context->getFunction($functionName)->getFunctionName();
        } else {
            if ($this->isSimpleAccessOperator($functionName)) {
                $object     = $functionName->getChildAt(0)->evaluate($context);
                $methodName = $functionName->getChildAt(1)->getName();

                $callback = [$object, $methodName];
            }
        }

        $arguments = array_map(
            function (Node $nodeInterface) use ($context) {
                return $nodeInterface->evaluate($context);
            },
            $this->getChildAt(1)->getChildren()
        );

        return call_user_func_array($callback, $arguments);
    }

    private function isSimpleAccessOperator(Node $node)
    {
        return $node instanceof BinaryOperatorNode &&
               $node->isOperator(SimpleAccessOperator::class);
    }
}