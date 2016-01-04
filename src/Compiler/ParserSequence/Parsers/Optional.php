<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\DelegateParser;
use Expresso\Compiler\TokenStream;

class Optional extends DelegateParser
{

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function canParse(TokenStream $stream)
    {
        return true;
    }

    /**
     * @param TokenStream $stream
     * @return \Generator
     */
    public function parse(TokenStream $stream)
    {
        // TODO: Implement parse() method.
    }
}