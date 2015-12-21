<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ExpressionParser extends Parser
{

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $parser->pushOperatorSentinel();
        yield $parser->parse('term');
        yield $parser->parse('binary');

        yield $parser->parse('conditional');
        $parser->popOperatorSentinel();
    }
}