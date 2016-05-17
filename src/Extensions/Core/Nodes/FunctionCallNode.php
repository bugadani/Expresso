<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\RuntimeFunction;
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
        if ($functionName instanceof CallableNode) {
            $this->isIndirectCall = !$functionName->inlineable();
        } else {
            $this->isIndirectCall = true;
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
            $functionName = $compiler->addTempVariable($functionName);
            $wrapper      = RuntimeFunction::class;
            $compiler->add("(({$functionName} === null) ? null : (new {$wrapper}({$functionName}))({$arguments}))");
        } else {
            $compiler->add("{$functionName}({$arguments})");
        }
    }

    public function evaluate(ExecutionContext $context)
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
