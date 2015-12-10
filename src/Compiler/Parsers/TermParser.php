<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

abstract class TermParser extends Parser
{
    public abstract function parseToken(Token $token);

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $parser->pushOperand($this->parseToken($stream->current()));
        $stream->next();

        yield $parser->parse('postfix');
    }
}