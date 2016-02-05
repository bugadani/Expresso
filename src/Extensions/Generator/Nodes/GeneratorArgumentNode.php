<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\EvaluationContext;

/**
 * Class GeneratorArgumentNode
 *
 * @package Expresso\Extensions\Generator\Nodes
 */
class GeneratorArgumentNode extends Node
{
    /**
     * @var
     */
    private $argumentName;

    /**
     * @var Node
     */
    private $sourceExpression;

    /**
     * GeneratorArgumentNode constructor.
     *
     * @param Token $argumentName
     * @param Node  $sourceExpression
     */
    public function __construct(Token $argumentName, Node $sourceExpression)
    {
        $this->argumentName     = $argumentName->getValue();
        $this->sourceExpression = $sourceExpression;
    }

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler)
    {
        $compiler->add(yield $compiler->compileNode($this->sourceExpression));
    }

    /**
     * @inheritdoc
     */
    public function evaluate(EvaluationContext $context)
    {
        return $this->sourceExpression->evaluate($context);
    }

    /**
     * @return mixed
     */
    public function getArgumentName()
    {
        return $this->argumentName;
    }
}
