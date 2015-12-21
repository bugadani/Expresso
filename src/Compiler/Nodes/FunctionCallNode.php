<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;

class FunctionCallNode extends Node
{

    private function isSimpleAccessOperator(Node $node)
    {
        return $node instanceof OperatorNode
               && $node->isOperator(SimpleAccessOperator::class);
    }

    public function __construct($functionName, ArgumentListNode $arguments)
    {
        if (!$functionName instanceof FunctionNameNode) {
            if ($functionName instanceof IdentifierNode) {
                $functionName = new FunctionNameNode($functionName->getName());
            } else if ($this->isSimpleAccessOperator($functionName)) {
                $functionName = new MethodNameNode($functionName);
            } else {
                throw new ParseException('Invalid function name');
            }
        }
        $this->addChild($functionName);
        $this->addChild($arguments);
    }

    public function addArgument(Node $node)
    {
        $this->getChildAt(1)->addChild($node);
    }

    public function compile(Compiler $compiler)
    {
        yield $this->getChildAt(0)->compile($compiler);
        $compiler->add('(');
        yield $this->getChildAt(1)->compile($compiler);
        $compiler->add(')');
    }

    public function evaluate(EvaluationContext $context)
    {
        $callback  = (yield $this->getChildAt(0)->evaluate($context));
        $arguments = (yield $this->getChildAt(1)->evaluate($context));

        $context->setReturnValue(call_user_func_array($callback, $arguments));
    }
}