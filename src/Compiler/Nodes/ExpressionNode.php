<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;

class ExpressionNode extends Node
{
    /**
     * @var string
     */
    private $expression;

    public function __construct($expression, Node $rootNode)
    {
        $this->expression = $expression;
        $this->addChild($rootNode);
    }

    public function compile(Compiler $compiler)
    {
        $this->expectChildCount(1);
        $compiler->add('function(array $context = []) {')
                 ->add('$context = new Expresso\\ExecutionContext($context);')
                 ->add('return ');

        yield $compiler->compileNode($this->getChildAt(0));

        $compiler->add(';};');
    }

    public function evaluate(EvaluationContext $context)
    {
        $retVal = (yield $this->getChildAt(0)->evaluate($context));

        yield $retVal;
    }
}