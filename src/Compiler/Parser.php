<?php

namespace Expresso\Compiler;

abstract class Parser
{
    abstract public function parse(TokenStream $stream, TokenStreamParser $parser);
}