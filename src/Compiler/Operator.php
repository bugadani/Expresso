<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Runtime\ExecutionContext;

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
     * @param ExecutionContext $context
     * @param Node $node
     * @return \Generator
     */
    public abstract function evaluate(ExecutionContext $context, Node $node);

    public abstract function compile(Compiler $compiler, Node $node);

    public abstract function createNode(CompilerConfiguration $config, Node ...$operands) : Node;

    public abstract function getOperandCount() : int;
}
