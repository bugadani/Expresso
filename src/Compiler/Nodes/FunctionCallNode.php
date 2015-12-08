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

            $compiler->add($extensionFunctionName)
                     ->add('(')
                     ->compileNode($this->getChildAt(1))
                     ->add(')');
        } else {
            if ($this->isSimpleAccessOperator()) {
                $object = $functionName->getChildAt(0);
                $method = $functionName->getChildAt(1);
                $compiler->compileNode($object)
                         ->add('->')
                         ->add($method->getName())
                         ->add('(')
                         ->compileNode($this->getChildAt(1))
                         ->add(')');
            }
        }
    }

    public function evaluate(EvaluationContext $context)
    {
        $arguments = array_map(
            function (Node $nodeInterface) use ($context) {
                return $nodeInterface->evaluate($context);
            },
            $this->getChildAt(1)->getChildren()
        );

        if ($this->getChildAt(0) instanceof IdentifierNode) {
            /** @var IdentifierNode $functionName */
            $functionName = $this->getChildAt(0);

            $callback = $context->getFunction($functionName->getName())->getFunctionName();

            return call_user_func_array($callback, $arguments);
        } else {
            if ($this->isSimpleAccessOperator()) {
                $object     = $this->getChildAt(0)->getChildAt(0)->evaluate($context);
                $methodName = $this->getChildAt(0)->getChildAt(1)->getName();

                return call_user_func_array([$object, $methodName], $arguments);
            }
        }
    }

    /**
     * @return bool
     */
    private function isSimpleAccessOperator()
    {
        return $this->getChildAt(0) instanceof BinaryOperatorNode &&
               $this->getChildAt(0)->isOperator(SimpleAccessOperator::class);
    }
}