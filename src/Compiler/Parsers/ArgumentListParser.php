<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ArgumentListParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $arguments = [];

        if (!$stream->nextTokenIf(Token::PUNCTUATION, ')')) {
            $currentToken = $stream->current();
            while (!$currentToken->test(Token::PUNCTUATION, ')')) {
                $parser->parse('expression');
                $arguments[]  = $parser->popOperand();
                $currentToken = $stream->expectCurrent(Token::PUNCTUATION, [',', ')']);
            }
        }

        $parser->pushOperand(new DataNode($arguments));
    }
}