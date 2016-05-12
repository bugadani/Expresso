<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;

use Expresso\Compiler\Node;
use Expresso\ExecutionContext;

class StatementNode extends Node
{
    /**
     * @var Node[]
     */
    private $expressions = [];

    public function __construct(array $expressions)
    {
        if (count($expressions) < 2) {
            throw new \BadMethodCallException('A statement must have at least 2 expressions');
        }
        $this->expressions = $expressions;
    }

    public function compile(Compiler $compiler)
    {
        foreach ($this->expressions as $expression) {
            $last = (yield $compiler->compileNode($expression, false));
        }
        $compiler->add($last);
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