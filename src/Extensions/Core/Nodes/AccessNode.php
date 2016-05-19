<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Node;
use Expresso\Runtime\Exceptions\AssignmentException;
use Expresso\Runtime\ExecutionContext;

abstract class AccessNode extends AssignableNode
{
    /**
     * @var Node
     */
    protected $left;

    /**
     * @var Node
     */
    protected $right;

    public function __construct(Node $left, Node $right)
    {
        $this->left  = $left;
        $this->right = $right;
    }

    public function evaluate(ExecutionContext $context)
    {
        $left  = (yield $this->left->evaluate($context));
        $right = (yield $this->right->evaluate($context));

        return $this->get($left, $right, false);
    }

    abstract protected function &get(&$container, $rightHand, bool $forAssign);

    abstract protected function assign(&$container, $leftHand, $rightHand);

    public function evaluateAssign(ExecutionContext $context, $value)
    {
        $parentStack = new \SplStack();

        $left = $this->left;
        while ($left instanceof AccessNode) {
            $parentStack->push($left);
            $left = $left->left;
        }
        if (!$left instanceof IdentifierNode) {
            throw new AssignmentException('Cannot assign to non-variable');
        }
        $container = &$context[ $left->getName() ];
        foreach ($parentStack as $parent) {
            $container = &$parent->get($container, yield $parent->right->evaluate($context), true);
        }
        $this->assign($container, yield $this->right->evaluate($context), $value);
    }
}