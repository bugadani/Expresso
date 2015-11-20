<?php

/**
 * This file is part of the Minty templating library.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Expresso\Compiler;

abstract class Node implements NodeInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var Node[]
     */
    private $children = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param Node $node
     * @param null $key
     *
     * @return Node
     */
    public function addChild(Node $node, $key = null)
    {
        if ($key === null) {
            $this->children[] = $node;
        } else {
            $this->children[ $key ] = $node;
        }

        return $node;
    }

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChild($key)
    {
        return isset($this->children[ $key ]);
    }

    public function getChild($key)
    {
        return $this->children[ $key ];
    }

    public function removeChild($key)
    {
        unset($this->children[ $key ]);
    }
}
