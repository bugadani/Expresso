<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;

class FunctionCallNode extends BinaryOperatorNode
{
    /**
     * @var FunctionNameNode|MethodNameNode
     */
    private $functionName;

    /**
     * @var ArgumentListNode
     */
    private $arguments;

    private $isIndirectCall = false;

    private function isSimpleAccessOperator(Node $node)
    {
        return $node instanceof OperatorNode
               && $node->isOperator(SimpleAccessOperator::class);
    }

    public function __construct(Node $functionName, ArgumentListNode $arguments)
    {
        if (!$functionName instanceof FunctionNameNode) {
            if ($functionName instanceof IdentifierNode) {
                $functionName = new FunctionNameNode($functionName->getName());
            } else if ($this->isSimpleAccessOperator($functionName)) {
                /** @var BinaryOperatorNode $functionName */
                $functionName = new MethodNameNode($functionName);
            } else {
                $this->isIndirectCall = true;
            }
        }

        $this->functionName = $functionName;
        $this->arguments    = $arguments;
    }

    public function compile(Compiler $compiler)
    {
        //Never inline indirect calls
        $functionName = (yield $compiler->compileNode($this->functionName, !$this->isIndirectCall));
        $arguments    = (yield $compiler->compileNode($this->arguments));

        $compiler->add("{$functionName}({$arguments})");
    }

    public function evaluate(EvaluationContext $context)
    {
        $callback  = (yield $this->functionName->evaluate($context));
        $arguments = (yield $this->arguments->evaluate($context));

        return call_user_func_array($callback, $arguments);
    }

    public function getChildren()
    {
        return [$this->functionName, $this->arguments];
    }

    /**
     * @return boolean
     */
    public function isIndirectCall()
    {
        return $this->isIndirectCall;
    }
}