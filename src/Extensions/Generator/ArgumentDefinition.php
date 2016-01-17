<?php

namespace Expresso\Extensions\Generator;

use Expresso\Compiler\Node;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Extensions\Generator\Generator\BranchPart;

class ArgumentDefinition extends BranchPart
{
    /**
     * @var Token
     */
    private $argument;

    /**
     * @var Node
     */
    private $source;

    /**
     * ArgumentDefinition constructor.
     *
     * @param Token $argument
     * @param Node  $source
     */
    public function __construct(Token $argument, Node $source)
    {
        $this->argument = $argument;
        $this->source = $source;
    }
}