<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class ExpressionNode extends Node
{
    /**
     * @var string
     */
    private $expression;

    /**
     * @var Node
     */
    private $rootNode;

    public function __construct($expression, Node $rootNode)
    {
        $this->expression = $expression;
        $this->rootNode   = $rootNode;
    }

    public function compile(Compiler $compiler)
    {
        $contextClass = ExecutionContext::class;
        $compiler->add("function({$contextClass} \$context) {");

        $bodyContext = (yield $compiler->compileNode($this->rootNode));
        $compiler->compileStatements();

        $compiler->add("return {$bodyContext};}");
    }

    public function evaluate(ExecutionContext $context)
    {
        return $this->rootNode->evaluate($context);
    }
}