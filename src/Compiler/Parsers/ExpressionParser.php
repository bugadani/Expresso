<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\SubParser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Parser;

class ExpressionParser extends SubParser
{

    public function parse(TokenStream $stream, Parser $parser)
    {
        $parser->pushOperatorSentinel();
        yield $parser->parse('term');
        yield $parser->parse('binary');

        yield $parser->parse('conditional');
        $parser->popOperatorSentinel();
    }
}