<?php

namespace Expresso;

class ExecutionContext implements \ArrayAccess
{
    /**
     * @var ExecutionContext
     */
    protected $parentContext;
    private $data;

    public function __construct($input, ExecutionContext $parentContext = null)
    {
        $this->data          = $input;
        $this->parentContext = $parentContext;
    }

    public function &access(&$where, $what)
    {
        if (is_array($where)) {
            return $where[ $what ];
        } else if (is_object($where)) {
            if (method_exists($where, $what)) {
                $methodWrapper = [$where, $what];

                return $methodWrapper;
            } else if ($where instanceof \ArrayAccess) {
                return $where[ $what ];
            } else {
                return $where->{$what};
            }
        }

        throw new \OutOfBoundsException("{$what} is not present in \$where");
    }

    public function createInnerScope($input)
    {
        return new ExecutionContext($input, $this);
    }

    /**
     * @inheritdoc
     */
    public function &offsetGet($index)
    {
        if (array_key_exists($index, $this->data)) {
            return $this->data[ $index ];
        }

        if ($this->parentContext !== null) {
            return $this->parentContext->offsetGet($index);
        }
        throw new \OutOfBoundsException("Array index out of bounds: {$index}");
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data)
        || ($this->parentContext !== null
            && $this->parentContext->offsetExists($offset));
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if ($this->parentContext !== null && $this->parentContext->offsetExists($offset)) {
            $this->parentContext->offsetSet($offset, $value);
        } else {
            $this->data[ $offset ] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        if ($this->parentContext !== null && $this->parentContext->offsetExists($offset)) {
            $this->parentContext->offsetUnset($offset);
        } else {
            unset($this->data[ $offset ]);
        }
    }

    /**
     * @return mixed
     */
    public function getArrayCopy()
    {
        return $this->data;
    }
}