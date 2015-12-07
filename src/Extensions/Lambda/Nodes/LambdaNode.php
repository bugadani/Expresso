<?php

namespace Expresso\Extensions\Lambda\Nodes;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\EvaluationContext;

class LambdaNode extends Node
{
    /**
     * @var Node
     */
    private $functionBody;

    /**
     * @var IdentifierNode[]
     */
    private $arguments;

    public function __construct(Node $functionBody, array $arguments)
    {
        $this->functionBody = $functionBody;
        $this->arguments    = $arguments;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->add('function(');

        $first = true;
        foreach ($this->arguments as $argName) {
            if (!$first) {
                $compiler->add(',');
            } else {
                $first = false;
            }
            $compiler->add('$' . $argName->getName());
        }

        $compiler->add(') use ($context) {')
                 ->add('$context = new Expresso\\ExecutionContext([');

        $first = true;
        foreach ($this->arguments as $argName) {
            if (!$first) {
                $compiler->add(',');
            } else {
                $first = false;
            }
            $compiler->compileString($argName->getName());
            $compiler->add('=> $' . $argName->getName());
        }
        $compiler->add('], $context);')
                 ->add('return ')
                 ->compileNode($this->functionBody)
                 ->add(';}');
    }

    public function evaluate(EvaluationContext $context)
    {
        return function () use ($context) {
            $arguments    = array_slice(func_get_args(), 0, count($this->arguments));
            $argNames     = array_map(
                function (IdentifierNode $node) {
                    return $node->getName();
                },
                $this->arguments
            );
            $innerContext = $context->createInnerScope(array_combine($argNames, $arguments));

            return $this->functionBody->evaluate($innerContext);
        };
    }
}