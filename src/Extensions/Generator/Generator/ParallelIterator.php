<?php

namespace Expresso\Extensions\Generator\Generator;

class ParallelIterator implements \Iterator
{
    const FIRST_FINISH = 0;
    const LAST_FINISH  = 1;

    /**
     * @var \Iterator[]
     */
    private $iterators = [];

    /**
     * @var int
     */
    private $operationMode;

    public function __construct($operationMode = self::FIRST_FINISH)
    {
        $this->operationMode = $operationMode;
    }

    public function addIterator(\Iterator $iterator)
    {
        $this->iterators[] = $iterator;
    }

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $values = [];
        foreach ($this->iterators as $iterator) {
            if ($iterator->valid()) {
                $values[] = $iterator->current();
            } else {
                $values[] = null;
            }
        }

        return $values;
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        foreach ($this->iterators as $iterator) {
            $iterator->next();
        }
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        $keys = [];
        foreach ($this->iterators as $iterator) {
            if ($iterator->valid()) {
                $keys[] = $iterator->key();
            } else {
                $keys[] = null;
            }
        }

        return $keys;
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        if ($this->operationMode === self::FIRST_FINISH) {
            foreach ($this->iterators as $iterator) {
                if (!$iterator->valid()) {
                    return false;
                }
            }

            return true;
        } else {
            foreach ($this->iterators as $iterator) {
                if ($iterator->valid()) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        foreach ($this->iterators as $iterator) {
            $iterator->rewind();
        }
    }
}