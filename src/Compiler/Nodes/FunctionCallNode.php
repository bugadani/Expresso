<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\ExecutionContext;

class FunctionCallNode extends Node
{
    /**
     * @var NodeInterface
     */
    private $functionName;

    /**
     * @var array
     */
    private $arguments;

    public function __construct($functionName, DataNode $arguments)
    {
        $this->functionName = $functionName;
        $this->arguments    = $arguments->getValue();
    }

    public function compile(Compiler $compiler)
    {
        // TODO: Implement compile() method.
    }

    public function evaluate(ExecutionContext $context)
    {

    }
}