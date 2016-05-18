<?php

namespace Expresso\Extensions\Core\Operators\Binary;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\Node;
use Expresso\Extensions\Core\Nodes\IdentifierNode;
use Expresso\Extensions\Core\Nodes\StringNode;
use Expresso\Runtime\Exceptions\TypeException;
use Expresso\Runtime\ExecutionContext;

class SimpleAccessOperator extends ArrayAccessOperator
{
    public function createNode(CompilerConfiguration $config, Node ...$operands): Node
    {
        list($left, $right) = $operands;
        if ($right instanceof IdentifierNode) {
            $right = new StringNode($right->getName());
        } else {
            throw new TypeException("Access operator requires a name on the right hand");
        }

        return parent::createNode($config, $left, $right);
    }

    public function evaluate(ExecutionContext $context, Node $node)
    {
        list($left, $right) = $node->getChildren();

        $left  = (yield $left->evaluate($context));
        $right = (yield $right->evaluate($context));

        return ExecutionContext::access($left, $right);
    }

    public function compileSimple(Compiler $compiler, $leftSource, $rightSource)
    {
        $class = ExecutionContext::class;
        $compiler->add("{$class}::access({$leftSource}, {$rightSource})");
    }
}