<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Operators\Binary\ArrayAccessOperator;
use Expresso\Compiler\Operators\Binary\SimpleAccessOperator;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

class VariableAccessNode extends Node
{
    /**
     * @var SimpleAccessOperator
     */
    private $operator;

    /**
     * @var NodeInterface
     */
    private $left;

    /**
     * @var NodeInterface
     */
    private $right;

    /**
     * VariableAccessNode constructor.
     * @param ArrayAccessOperator $operator
     * @param NodeInterface $left
     * @param NodeInterface $right
     */
    public function __construct(ArrayAccessOperator $operator, NodeInterface $left, NodeInterface $right)
    {
        $this->operator = $operator;
        $this->left     = $left;
        $this->right    = $right;
    }

    public function compile(Compiler $compiler)
    {
        $this->operator->compile($compiler, $this->left, $this->right);
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->operator->execute(
            $context,
            $this->left,
            $this->right
        );
    }
}