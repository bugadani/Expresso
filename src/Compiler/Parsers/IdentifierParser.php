<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class IdentifierParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $identifier = new IdentifierNode($currentToken->getValue());
        $parser->pushOperand($identifier);
        $stream->next();
        $parser->parse('postfix');
    }
}