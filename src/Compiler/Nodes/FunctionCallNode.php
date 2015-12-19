<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeTreeEvaluator;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;

class FunctionCallNode extends Node
{

    public function __construct($functionName, array $arguments = [])
    {
        if ($functionName instanceof IdentifierNode) {
            $functionName = new FunctionNameNode($functionName->getName());
        } else if ($this->isSimpleAccessOperator($functionName)) {
            $functionName = new MethodNameNode($functionName);
        } else {
            throw new ParseException('Invalid function name');
        }
        $this->addChild($functionName);
        $this->addChild(new ArgumentListNode($arguments));
    }

    public function addArgument(Node $node)
    {
        $this->getChildAt(1)->addChild($node);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->compileNode($this->getChildAt(0))
            ->add('(')
            ->compileNode($this->getChildAt(1))
            ->add(')');
    }

    public function evaluate(EvaluationContext $context)
    {
        $callback  = (yield $this->getChildAt(0)->evaluate($context));
        $arguments = (yield $this->getChildAt(1)->evaluate($context));

        $context->setReturnValue(call_user_func_array($callback, $arguments));
    }

    private function isSimpleAccessOperator(Node $node)
    {
        return $node instanceof BinaryOperatorNode
               && $node->isOperator(SimpleAccessOperator::class);
    }
}