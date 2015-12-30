<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\SubParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Parser;

abstract class TermParser extends SubParser
{
    public abstract function parseToken(Token $token);

    public function parse(TokenStream $stream, Parser $parser)
    {
        $parser->pushOperand($this->parseToken($stream->current()));
        $stream->next();

        yield $parser->parse('postfix');
    }
}