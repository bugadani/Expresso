<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\CurriedFunctionWrapper;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\ExecutionContext;

class FunctionCallNode extends BinaryOperatorNode
{
    /**
     * @var CallableNode|IdentifierNode
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
        $functionName = (yield $compiler->compileNode($this->functionName));
        $arguments    = (yield $compiler->compileNode($this->arguments));
        if ($this->isIndirectCall) {
            //Never inline indirect calls
            $functionName = (string)$compiler->addTempVariable($functionName);
        } else {
            $functionName = (string)$functionName;
        }

        $wrapper = CurriedFunctionWrapper::class;
        if ($functionName[0] === '$') {
            $wrapper             = CurriedFunctionWrapper::class;
            $wrappedFunctionName = "(new {$wrapper}({$functionName}))";
        } else {
            $wrappedFunctionName = "(new {$wrapper}('{$functionName}'))";
        }

        if ($this->isIndirectCall) {
            $compiler->add("(({$functionName} === null) ? null : ({$wrappedFunctionName})({$arguments}))");
        } else {
            $compiler->add("{$wrappedFunctionName}({$arguments})");
        }
    }

    public function evaluate(ExecutionContext $context)
    {
        $callback = (yield $this->functionName->evaluate($context));
        if ($callback === null) {
            return null;
        }
        $arguments = (yield $this->arguments->evaluate($context));

        if (!$callback instanceof CurriedFunctionWrapper) {
            $callback = new CurriedFunctionWrapper($callback);
        }

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
