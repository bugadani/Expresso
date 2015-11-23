<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\ExecutionContext;

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
        $compiler->add('function(array $context = []) {')
                 ->add('$context = new Expresso\\ExecutionContext($context);')
                 ->add('return ')
                 ->compileNode($this->rootNode)
                 ->add(';};');
    }

    public function evaluate(ExecutionContext $context)
    {
        return $this->rootNode->evaluate($context);
    }
}