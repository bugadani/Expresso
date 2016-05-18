<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Runtime\Exceptions\ConstantCallException;
use Expresso\Runtime\RuntimeFunction;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Runtime\ExecutionContext;

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

    /**
     * @var bool
     */
    private $isIndirectCall = false;

    public function __construct(Node $functionName, ArgumentListNode $arguments)
    {
        if ($functionName instanceof CallableNode) {
            $this->isIndirectCall = !$functionName->inlineable();
        } else if($functionName instanceof DataNode) {
            throw new ConstantCallException("Function name cannot be a constant");
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
