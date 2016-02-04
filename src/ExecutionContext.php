<?php

namespace Expresso;

class ExecutionContext extends \ArrayObject
{
    /**
     * @var ExecutionContext
     */
    protected $parentContext;

    public function __construct($input, ExecutionContext $parentContext = null)
    {
        parent::__construct($input);
        $this->parentContext = $parentContext;
    }

    public function access($where, $what)
    {
        if (is_array($where)) {
            return $where[ $what ];
        }

        throw new \OutOfBoundsException("{$what} is not present in \$where");
    }

    public function createInnerScope($input)
    {
        return new ExecutionContext($input, $this);
    }

    public function offsetGet($index)
    {
        if (parent::offsetExists($index)) {
            return parent::offsetGet($index);
        }

        if ($this->parentContext !== null) {
            return $this->parentContext->offsetGet($index);
        }
        throw new \OutOfBoundsException("Array index out of bounds: {$index}");
    }
}