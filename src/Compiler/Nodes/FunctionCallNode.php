<?php

namespace Expresso\Compiler\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\EvaluationContext;
use Expresso\Extensions\Core\Operators\Binary\SimpleAccessOperator;

class FunctionCallNode extends BinaryOperatorNode
{
    /**
     * @var FunctionNameNode|MethodNameNode
     */
    private $functionName;

    /**
     * @var ArgumentListNode
     */
    private $arguments;

    private $isIndirectCall = false;

    private function isSimpleAccessOperator(Node $node)
    {
        return $node instanceof OperatorNode
               && $node->isOperator(SimpleAccessOperator::class);
    }

    public function __construct(Node $functionName, ArgumentListNode $arguments)
    {
        if (!$functionName instanceof FunctionNameNode) {
            if ($functionName instanceof IdentifierNode) {
                $functionName = new FunctionNameNode($functionName->getName());
            } else if ($this->isSimpleAccessOperator($functionName)) {
                /** @var BinaryOperatorNode $functionName */
                $functionName = new MethodNameNode($functionName);
            } else {
                $this->isIndirectCall = true;
            }
        }

        $this->functionName = $functionName;
        $this->arguments    = $arguments;
    }

    public function compile(Compiler $compiler)
    {
        $functionName = (yield $compiler->compileNode($this->functionName));
        $arguments    = (yield $compiler->compileNode($this->arguments));

        if ($this->isIndirectCall) {
            //Never inline indirect calls
            $functionNameSource = $compiler->addTempVariable($functionName);
        } else {
            $functionNameSource = $functionName->source;
        }

        $compiler->add($functionNameSource);
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

    /**
     * @return boolean
     */
    public function isIndirectCall()
    {
        return $this->isIndirectCall;
    }
}