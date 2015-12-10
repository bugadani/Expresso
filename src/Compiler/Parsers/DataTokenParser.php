<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Token;

class DataTokenParser extends TermParser
{
    public function parseToken(Token $token)
    {
        return new DataNode($token->getValue());
    }
}