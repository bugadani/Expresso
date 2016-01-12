<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

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
        $bodyContext = (yield $compiler->compileNode($this->rootNode));

        $compiler->add('function(array $context = []) {')
                 ->add('$context = new Expresso\\ExecutionContext($context);');

        $compiler->compileTempVariables();

        $compiler->add('return ')
                 ->add($bodyContext->source)
                 ->add(';};');
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->rootNode->evaluate($context);
    }
}