<?php

namespace Expresso\Test\Extensions\Generator\Iterators;

use Expresso\Extensions\Generator\Iterators\WrappingIterator;

class WrappingIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testWrappingIterator()
    {
        $iterator = new WrappingIterator();
        $iterator->addIterator(new \ArrayIterator([1, 2]));
        $iterator->addIterator(new \ArrayIterator([1, 2]));

        $result = [];
        foreach ($iterator as $item) {
            $result[] = $item;
        }

        $this->assertEquals(
            [
                [1, 1],
                [1, 2],
                [2, 1],
                [2, 2]
            ],
            $result
        );
    }

    public function testWrappingIteratorWithKeys()
    {
        $iterator = new WrappingIterator();
        $iterator->addIterator(new \ArrayIterator([1, 2]), 'a');
        $iterator->addIterator(new \ArrayIterator([1, 2]), 'b');

        $result = [];
        foreach ($iterator as $item) {
            $result[] = $item;
        }

        $this->assertEquals(
            [
                ['a' => 1, 'b' => 1],
                ['a' => 1, 'b' => 2],
                ['a' => 2, 'b' => 1],
                ['a' => 2, 'b' => 2]
            ],
            $result
        );
    }
}
