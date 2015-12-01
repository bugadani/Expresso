<?php

namespace Expresso;

class ExecutionContext extends \ArrayObject
{
    /**
     * @var ExecutionContext
     */
    private $parentContext;

    public function __construct($input, ExecutionContext $parentContext = null)
    {
        parent::__construct($input);
        $this->parentContext = $parentContext;
    }

    public function access($where, $what, $nullSafe = false)
    {
        if (is_array($where)) {
            if (!$nullSafe || isset($where[ $what ])) {
                return $where[ $what ];
            }
        }
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