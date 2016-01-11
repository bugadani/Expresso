<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Exceptions\ParseException;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;

class FunctionCallNode extends Node
{
    /**
     * @var FunctionNameNode|MethodNameNode
     */
    private $functionName;

    /**
     * @var ArgumentListNode
     */
    private $arguments;

    private function isSimpleAccessOperator(Node $node)
    {
        return $node instanceof OperatorNode
               && $node->isOperator(SimpleAccessOperator::class);
    }

    public function __construct($functionName, ArgumentListNode $arguments)
    {
        if (!$functionName instanceof FunctionNameNode) {
            if ($functionName instanceof IdentifierNode) {
                $functionName = new FunctionNameNode($functionName->getName());
            } else if ($this->isSimpleAccessOperator($functionName)) {
                $functionName = new MethodNameNode($functionName);
            } else {
                throw new ParseException('Invalid function name');
            }
        }

        $this->functionName = $functionName;
        $this->arguments    = $arguments;
    }

    public function compile(Compiler $compiler)
    {
        $functionName = (yield $compiler->compileNode($this->functionName));
        $arguments    = (yield $compiler->compileNode($this->arguments));

        $compiler->add($functionName->source);
        $compiler->add('(');
        $compiler->add($arguments->source);
        $compiler->add(')');
    }

    public function evaluate(EvaluationContext $context)
    {
        $callback  = (yield $this->functionName->evaluate($context));
        $arguments = (yield $this->arguments->evaluate($context));

        $retVal = call_user_func_array($callback, $arguments);
        if ($retVal instanceof \Generator) {
            $retVal = new \IteratorIterator($retVal);
        }

        yield $retVal;
    }

    public function getChildren()
    {
        return [$this->functionName, $this->arguments];
    }
}