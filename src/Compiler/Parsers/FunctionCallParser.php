<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class FunctionCallParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        //either identifier or access node
        $node = $parser->popOperand();

        $parser->parse('argumentList');
        $arguments = $parser->popOperand();
        $node = new FunctionCallNode($node, $arguments);

        $stream->expectCurrent(Token::PUNCTUATION, ')');

        $parser->pushOperand($node);

        $stream->next();
        $parser->parse('postfix');
    }
}