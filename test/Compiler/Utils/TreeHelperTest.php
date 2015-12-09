<?php

namespace Expresso\Test\Compiler\Utils;

use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Utils\TreeHelper;

class TreeHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testTraverse()
    {
        $preorder  = [];
        $postorder = [];

        $preOrderFn = function (DataNode $node) use (&$preorder) {
            $preorder[] = $node->getValue();
        };

        $postOrderFn = function (DataNode $node) use (&$postorder) {
            $postorder[] = $node->getValue();
        };

        $node1 = new DataNode(1);
        $node2 = new DataNode(2);
        $node3 = new DataNode(3);
        $node4 = new DataNode(4);
        $node5 = new DataNode(5);
        $node6 = new DataNode(6);

        $node1->addChild($node2);
        $node1->addChild($node3);

        $node2->addChild($node4);
        $node2->addChild($node5);

        $node3->addChild($node6);

        TreeHelper::traverse($node1, $preOrderFn, $postOrderFn);

        $this->assertEquals([1, 2, 4, 5, 3, 6], $preorder);
        $this->assertEquals([4, 5, 2, 6, 3, 1], $postorder);
    }

    public function testTraverseSkipSubTree()
    {
        $preorder  = [];
        $postorder = [];

        $preOrderFn = function (DataNode $node) use (&$preorder) {
            $preorder[] = $node->getValue();

            return $node->getValue() != 3;
        };

        $postOrderFn = function (DataNode $node) use (&$postorder) {
            $postorder[] = $node->getValue();
        };

        $node1 = new DataNode(1);
        $node2 = new DataNode(2);
        $node3 = new DataNode(3);
        $node4 = new DataNode(4);
        $node5 = new DataNode(5);
        $node6 = new DataNode(6);

        $node1->addChild($node2);
        $node1->addChild($node3);

        $node2->addChild($node4);
        $node2->addChild($node5);

        $node3->addChild($node6);

        TreeHelper::traverse($node1, $preOrderFn, $postOrderFn);

        $this->assertEquals([1, 2, 4, 5, 3], $preorder);
        $this->assertEquals([4, 5, 2, 1], $postorder);
    }
}