<?php

namespace Expresso\Extensions\Generator;

use Expresso\Compiler\Node;
use Expresso\Extensions\Generator\Generator\BranchPart;

class Filter extends BranchPart
{
    /**
     * @var Node
     */
    private $expression;

    /**
     * Filter constructor.
     *
     * @param Node $expression
     */
    public function __construct(Node $expression)
    {
        $this->expression = $expression;
    }
}