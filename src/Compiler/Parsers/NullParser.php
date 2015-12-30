<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\SubParser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Parser;

class NullParser extends SubParser
{
    /**
     * @param TokenStream $stream
     * @param Parser      $parser
     *
     * @return \Generator
     */
    public function parse(TokenStream $stream, Parser $parser)
    {
        yield;
    }
}