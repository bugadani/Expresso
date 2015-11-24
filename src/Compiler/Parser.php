<?php

namespace Expresso\Compiler;

abstract class Parser
{
    abstract public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser);
}