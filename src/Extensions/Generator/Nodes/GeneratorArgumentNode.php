<?php

namespace Expresso\Extensions\Generator\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\EvaluationContext;

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

    public function __construct(Token $argumentName, Node $sourceExpression)
    {
        $this->argumentName     = $argumentName->getValue();
        $this->sourceExpression = $sourceExpression;
    }

    public function compile(Compiler $compiler)
    {
        $compiledArgument = (yield $compiler->compileNode($this->sourceExpression));
        $compiler->add($compiledArgument->source);
    }

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
