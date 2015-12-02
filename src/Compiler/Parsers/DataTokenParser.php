<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class DataTokenParser extends Parser
{
    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $node = new DataNode($stream->current()->getValue());
        $parser->pushOperand($node);
        $stream->next();

        $parser->parse('postfix');
    }
}