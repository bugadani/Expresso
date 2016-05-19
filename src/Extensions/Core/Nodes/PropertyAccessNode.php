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
        if (!$this->left instanceof AssignableNode) {
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

    protected function &get(&$container, $rightHand)
    {
        return ExecutionContext::access($container, $rightHand);
    }

    protected function contains($container, $leftHand) : bool
    {
        if (is_array($container)) {
            return isset($container[ $leftHand ]);
        } else {
            return (method_exists($container, $leftHand) || property_exists($container, $leftHand));
        }
    }

    protected function assign(&$container, $leftHand, $rightHand)
    {
        if (is_object($container)) {
            $container->{$leftHand} = $rightHand;
        } else {
            $container[ $leftHand ] = $rightHand;
        }
    }

    public function compileContains(Compiler $compiler)
    {
        $contextClass = ExecutionContext::class;
        $compiler->pushContext();
        $tempVar = $compiler->requestTempVariable();
        $compiler->add('try {');
        $compiler->add("{$contextClass}::access(")
                 ->add(yield $compiler->compileNode($this->left))
                 ->add(', ')
                 ->add(yield $compiler->compileNode($this->right))
                 ->add(');');
        $compiler->add("{$tempVar} = true;} catch(\\OutOfBoundsException \$e) {{$tempVar} = false;}");
        $compiler->addStatement($compiler->popContext());
        $compiler->add($tempVar);
    }
}