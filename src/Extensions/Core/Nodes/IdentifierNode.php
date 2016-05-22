<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class IdentifierNode extends VariableNode
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function compile(Compiler $compiler)
    {
        $compiler->addVariableAccess($this->value);
    }

    public function evaluate(ExecutionContext $context)
    {
        return $context[ $this->value ];
    }

    public function compileAssign(Compiler $compiler, Node $rightHand)
    {
        $compiler->add(yield $compiler->compileNode($this))
                 ->add(' = ')
                 ->add(yield $compiler->compileNode($rightHand));
    }

    public function evaluateAssign(ExecutionContext $context, $value)
    {
        return $context[ $this->value ] = $value;
    }

    public function getName() : string
    {
        return $this->value;
    }

    public function compileContains(Compiler $compiler)
    {
        $compiler->add('isset(')
                 ->add(yield $compiler->compileNode($this))
                 ->add(')');
    }

    public function evaluateContains(ExecutionContext $context)
    {
        return isset($context[ $this->value ]);
    }
}