<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\ExecutionContext;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;

class ExpressionNode extends Node
{
    /**
     * @var string
     */
    private $expression;

    /**
     * @var NodeInterface
     */
    private $rootNode;

    public function __construct($expression, NodeInterface $rootNode)
    {
        $this->expression = $expression;
        $this->rootNode   = $rootNode;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('function() {')
                 ->add('return ')
                 ->compileNode($this->rootNode)
                 ->add(';};');
    }

    public function evaluate(ExecutionContext $context)
    {
        return $this->rootNode->evaluate($context);
    }
}