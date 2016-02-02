<?php

namespace Expresso\Extensions\Generator\Iterators;

/**
 * WrappingIterator implements a method for iterators to wrap around if one finishes, similarly to a
 * metering device. If the inner most iterator is done, it is reset and the next one is moved to its next element.
 *
 * This will function as a pair of MultipleIterator which iterates in a parallel way; this one
 * iterates elements in a serial way.
 *
 * @package Expresso\Extensions\Generator\Generator\Iterators
 */
class WrappingIterator implements \Iterator
{
    /**
     * @var \Iterator[]
     */
    private $iterators = [];
    private $iteratorList;
    private $index;

    /**
     * WrappingIterator constructor.
     */
    public function __construct()
    {
        $this->iteratorList = new \SplDoublyLinkedList();

        //Stack-like iteration mode so that rewind goes to the end of the list
        $this->iteratorList->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO);
    }

    /**
     * @param \Traversable $iterator
     * @param null         $key
     */
    public function addIterator(\Traversable $iterator, $key = null)
    {
        if ($key === null) {
            $this->iterators[] = $iterator;
        } else {
            $this->iterators[ $key ] = $iterator;
        }
        $this->iteratorList->push($iterator);
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
        foreach ($this->iterators as $key => $iterator) {
            $values[ $key ] = $iterator->current();
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
        $currentIterator = $this->iteratorList->current();
        $currentIterator->next();

        //wrap-around
        while (!$currentIterator->valid() && $currentIterator !== $this->iteratorList->bottom()) {
            $currentIterator->rewind();

            $this->iteratorList->next();
            $currentIterator = $this->iteratorList->current();

            $currentIterator->next();
        }

        $this->iteratorList->rewind();
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
        return $this->index;
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
        return $this->iteratorList->bottom()->valid();
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
        $this->iteratorList->rewind();

        foreach ($this->iterators as $iterator) {
            $iterator->rewind();
        }

        $this->index = 0;
    }
}