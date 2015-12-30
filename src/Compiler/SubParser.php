<?php

namespace Expresso\Compiler;

abstract class SubParser
{
    /**
     * @param TokenStream $stream
     * @param Parser      $parser
     *
     * @return \Generator
     */
    abstract public function parse(TokenStream $stream, Parser $parser);
}