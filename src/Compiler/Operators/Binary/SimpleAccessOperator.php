<?php

namespace Expresso\Compiler\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\NodeInterface;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\VariableAccessNode;
use Expresso\EvaluationContext;
use Expresso\ExecutionContext;

class SimpleAccessOperator extends ArrayAccessOperator
{

    public function operators()
    {
        return '.';
    }

    public function createNode($left, $right)
    {
        return new VariableAccessNode($this, $left, $right);
    }

    public function execute(EvaluationContext $context, NodeInterface $left, NodeInterface $right)
    {
        $left = $left->evaluate($context);

        if ($right instanceof IdentifierNode) {
            $right = $right->getName();
        } else {
            $right = $right->evaluate($context);
        }

        return $left[ $right ];
    }

    public function compile(Compiler $compiler, NodeInterface $left, NodeInterface $right)
    {
        $compiler->add('$context->access(')
                 ->compileNode($left)
                 ->add(', ');

        if ($right instanceof IdentifierNode) {
            $compiler->compileString($right->getName());
        } else {
            $compiler->compileNode($right);
        }

        $compiler->add(')');
    }
}