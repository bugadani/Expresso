<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ExpressionParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $parser->pushOperatorSentinel();

        $parser->parse('term');
        $parser->parse('binary');

        $parser->popOperatorSentinel();

        if ($parser->hasParser('conditional')) {
            $parser->parse('conditional');
        }
    }
}