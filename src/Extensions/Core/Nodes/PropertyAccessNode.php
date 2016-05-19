<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\Exceptions\AssignmentException;
use Expresso\Runtime\Exceptions\TypeException;
use Expresso\Runtime\ExecutionContext;

class PropertyAccessNode extends AccessNode
{
    public function __construct(Node $left, Node $right)
    {
        if ($right instanceof IdentifierNode) {
            $right = new StringNode($right->getName());
        } else {
            throw new TypeException("Access operator requires a name on the right hand");
        }
        parent::__construct($left, $right);
    }

    public function compileAssign(Compiler $compiler, Node $rightHand)
    {
        if (!$this->left instanceof AssignableNode && !$this->left instanceof IdentifierNode) {
            throw new AssignmentException('Cannot assign to non-variable');
        }
        $source       = yield $compiler->compileNode($this);
        $tempVariable = $compiler->addTempVariable("&{$source}");

        $compiler->add("{$tempVariable} = ")
                 ->add(yield $compiler->compileNode($rightHand));
    }

    public function compile(Compiler $compiler)
    {
        $contextClass = ExecutionContext::class;
        $compiler->add("{$contextClass}::access(")
                 ->add(yield $compiler->compileNode($this->left))
                 ->add(', ')
                 ->add(yield $compiler->compileNode($this->right))
                 ->add(')');
    }

    public function evaluate(ExecutionContext $context)
    {
        $left  = (yield $this->left->evaluate($context));
        $right = (yield $this->right->evaluate($context));

        return $this->get($left, $right, false);
    }

    protected function &get(&$container, $rightHand, bool $forAssign)
    {
        return ExecutionContext::access($container, $rightHand);
    }

    protected function assign(&$container, $leftHand, $rightHand)
    {
        if (is_object($container)) {
            $container->{$leftHand} = $rightHand;
        } else {
            $container[ $leftHand ] = $rightHand;
        }
    }
}