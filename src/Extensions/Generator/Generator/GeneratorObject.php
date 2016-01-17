<?php

namespace Expresso\Extensions\Generator\Generator;

class GeneratorObject
{
    private $branches = [];

    public function addBranch(Branch $branch)
    {
        $this->branches[] = $branch;
    }
}