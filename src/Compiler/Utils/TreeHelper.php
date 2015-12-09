<?php

namespace Expresso\Compiler\Utils;

use Expresso\Compiler\Node;

class TreeHelper
{
    /**
     * @param Node $node
     * @param callable|null $preOrder A function that is called when a node is first visited. Return false to skip its children and skip calling $postOrder.
     * @param callable|null $postOrder
     */
    public static function traverse(Node $node, callable $preOrder = null, callable $postOrder = null)
    {
        if ($preOrder === null || $postOrder === null) {
            $nullFn = function () {
            };

            if ($preOrder === null) {
                $preOrder = $nullFn;
            }
            if ($postOrder === null) {
                $postOrder = $nullFn;
            }
        }

        $nodeStack     = new \SplStack();
        $childrenStack = new \SplStack();

        $preOrder($node);
        $nodeStack->push($node);
        $childrenStack->push($node->getChildren());

        while (!$nodeStack->isEmpty()) {
            $children = $childrenStack->pop();
            if (!empty($children)) {
                $child = array_shift($children);
                $childrenStack->push($children);

                if ($preOrder($child) !== false) {
                    $nodeStack->push($child);
                    $childrenStack->push($child->getChildren());
                }
            } else {
                $node = $nodeStack->pop();
                $postOrder($node);
            }
        }
    }
}