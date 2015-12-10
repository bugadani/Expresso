<?php

namespace Expresso\Compiler;

abstract class Parser
{
    /**
     * @param TokenStream $stream
     * @param TokenStreamParser $parser
     * @return \Generator
     */
    abstract public function parse(TokenStream $stream, TokenStreamParser $parser);
}