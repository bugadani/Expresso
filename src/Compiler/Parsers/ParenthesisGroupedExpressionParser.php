<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\SubParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Parser;

class ParenthesisGroupedExpressionParser extends SubParser
{

    public function parse(TokenStream $stream, Parser $parser)
    {
        $stream->next();
        yield $parser->parse('expression');
        $stream->expectCurrent(Token::PUNCTUATION, ')');
        $stream->next();
    }
}