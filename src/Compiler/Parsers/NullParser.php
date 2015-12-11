<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class NullParser extends Parser
{
    /**
     * @param TokenStream $stream
     * @param TokenStreamParser $parser
     * @return \Generator
     */
    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        yield;
    }
}