<?php

namespace Expresso\Extensions\Generator\Generator;

use Expresso\Extensions\Generator\Generator\Iterators\ParallelIterator;

class GeneratorObject extends ParallelIterator
{
    public function addBranch(Branch $branch)
    {
        $this->addIterator($branch);
    }
}