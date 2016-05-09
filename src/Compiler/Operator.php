<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\EvaluationContext;

abstract class Operator
{
    const LEFT = 0;
    const RIGHT = 1;
    const NONE = 2;

    private $precedence;
    private $associativity;

    public function __construct(int $precedence, int $associativity = self::LEFT)
    {
        $this->precedence    = $precedence;
        $this->associativity = $associativity;
    }

    public function getPrecedence() : int
    {
        return $this->precedence;
    }

    /**
     * @return int
     */
    public function getAssociativity() : int
    {
        return $this->associativity;
    }

    /**
     * @param EvaluationContext $context
     * @param Node $node
     *
     * @return \Generator
     */
    public abstract function evaluate(EvaluationContext $context, Node $node);

    public abstract function compile(Compiler $compiler, Node $node);

    public abstract function createNode(CompilerConfiguration $config, Node ...$operands) : Node;

    public abstract function getOperandCount() : int;
}
