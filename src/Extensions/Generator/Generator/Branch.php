<?php

namespace Expresso\Extensions\Generator\Generator;

use Expresso\Extensions\Generator\Filter;
use Traversable;

class Branch implements \IteratorAggregate
{
    private $arguments = [];
    private $filters   = [];

    public function addArgument(BranchPart $part)
    {
    }

    public function addFilter(Filter $filter)
    {
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \CallbackFilterIterator(
            $this->iterator,
            function ($item) {
                foreach ($this->filters as $filter) {
                    if (!$filter($item)) {
                        return false;
                    }
                }

                return true;
            }
        );
    }
}
