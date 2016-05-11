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

    public function __construct(Node $functionName, ArgumentListNode $arguments)
    {
        if (!$functionName instanceof CallableNode) {
            if ($functionName instanceof IdentifierNode) {
                $functionName = new FunctionNameNode($functionName->getName());
            } else {
                $this->isIndirectCall = true;
            }
        } else {
            $this->isIndirectCall = !$functionName->inlineable();
        }

        $this->functionName = $functionName;
        $this->arguments    = $arguments;
    }

    public function compile(Compiler $compiler)
    {
        //Never inline indirect calls
        $functionName = (yield $compiler->compileNode($this->functionName, !$this->isIndirectCall));
        $arguments    = (yield $compiler->compileNode($this->arguments));

        if ($this->isIndirectCall) {
            $compiler->add("(({$functionName} === null) ? null : {$functionName}({$arguments}))");
        } else {
            $compiler->add("{$functionName}({$arguments})");
        }
    }

    public function evaluate(EvaluationContext $context)
    {
        $callback = (yield $this->functionName->evaluate($context));
        if ($callback === null) {
            return null;
        }
        $arguments = (yield $this->arguments->evaluate($context));

        return $callback(...$arguments);
    }

    public function getChildren() : array
    {
        return [$this->functionName, $this->arguments];
    }

    /**
     * @return boolean
     */
    public function isIndirectCall() : bool
    {
        return $this->isIndirectCall;
    }
}
