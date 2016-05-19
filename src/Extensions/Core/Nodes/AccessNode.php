<?php

namespace Expresso\Extensions\Core\Nodes;

use Expresso\Compiler\Node;
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

    public function evaluateAssign(ExecutionContext $context, $value)
    {
        $parentStack = [];

        $left = $this->left;
        while ($left instanceof AccessNode) {
            $parentStack[] = $left;
            $left          = $left->left;
        }
        $container = &$context[ $left->getName() ];
        while (!empty($parentStack)) {
            $parent    = array_pop($parentStack);
            $container = &ExecutionContext::access($container, yield $parent->right->evaluate($context));
        }
        $container[yield $this->right->evaluate($context)] = $value;
    }
}