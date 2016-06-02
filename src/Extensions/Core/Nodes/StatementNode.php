<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;

use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class StatementNode extends Node
{
    /**
     * @var Node[]
     */
    private $expressions = [];

    public function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    public function compile(Compiler $compiler)
    {
        $last = array_pop($this->expressions);
        //compile expressions into temp variables
        foreach ($this->expressions as $expression) {
            $compiler->addTempVariable(yield $compiler->compileNode($expression));
        }

        //the last expression is compiled directly
        $compiler->add(yield $compiler->compileNode($last));
    }

    public function evaluate(ExecutionContext $context)
    {
        foreach ($this->expressions as $expression) {
            $last = (yield $expression->evaluate($context));
        }
        return $last;
    }

    public function getChildren() : array
    {
        return $this->expressions;
    }
}