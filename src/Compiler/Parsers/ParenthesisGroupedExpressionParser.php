<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ParenthesisGroupedExpressionParser extends Parser
{

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $stream->next();
        yield $parser->parse('expression');
        $stream->expectCurrent(Token::PUNCTUATION, ')');
        $stream->next();
    }
}