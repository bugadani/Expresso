<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler;
use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\EvaluationContext;

class SimpleAccessOperator extends ArrayAccessOperator
{
    public function createNode(CompilerConfiguration $config, $left, $right)
    {
        if ($right instanceof IdentifierNode) {
            $right = new DataNode($right->getName());
        }

        return parent::createNode($config, $left, $right);
    }

    public function operators()
    {
        return '.';
    }

    public function compile(Compiler $compiler, Node $left, Node $right)
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