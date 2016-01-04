<?php

namespace Expresso\Compiler\ParserSequence;

use Expresso\Compiler\Node;
use Expresso\Compiler\TokenStream;

abstract class Parser
{
    public static function create()
    {
        return new static();
    }

    protected function __construct()
    {
    }

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    abstract public function canParse(TokenStream $stream);

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    abstract public function parse(TokenStream $stream);
}