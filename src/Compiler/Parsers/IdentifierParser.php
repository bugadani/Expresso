<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Token;

class IdentifierParser extends TermParser
{
    public function parseToken(Token $token)
    {
        return new IdentifierNode($token->getValue());
    }
}