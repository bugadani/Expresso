<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Utils\TreeHelper;
use Expresso\EvaluationContext;

abstract class Node
{
    /**
     * @var Node[]
     */
    private $children = [];

    /**
     * @var array A map of arbitrary extra data
     */
    private $data = [];

    public function addData($key, $value = false)
    {
        $this->data[ $key ] = $value;
    }

    public function removeData($key)
    {
        unset($this->data[ $key ]);
    }

    public function addDataRecursive($key, $value = false)
    {
        TreeHelper::traverse(
            $this,
            function (Node $node) use ($key, $value) {
                $node->addData($key, $value);
            }
        );
    }

    public function removeDataRecursive($key)
    {
        TreeHelper::traverse(
            $this,
            function (Node $node) use ($key) {
                $node->removeData($key);
            }
        );
    }

    public function hasData($key)
    {
        return isset($this->data[ $key ]);
    }

    public function getData($key)
    {
        return $this->data[ $key ];
    }

    abstract public function compile(Compiler $compiler);

    abstract public function evaluate(EvaluationContext $context, array $childResults);

    /**
     * @param Node $child
     */
    public function addChild(Node $child)
    {
        $this->children[] = $child;
    }

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getChildAt($index)
    {
        return $this->children[ $index ];
    }

    /**
     * @return array
     */
    public function getChildCount()
    {
        return count($this->children);
    }

    public function expectChildCount($count)
    {
        $actualCount = count($this->children);
        if (!$actualCount === $count) {
            throw new \UnexpectedValueException("This node expects {$count} children but has {$actualCount}");
        }
    }
}
