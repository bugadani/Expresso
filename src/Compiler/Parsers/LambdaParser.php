<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class LambdaParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $currentToken = $stream->next();

        $arguments = [];
        if ($currentToken->test(Token::PUNCTUATION, '(')) {
            if (!$stream->nextTokenIf(Token::PUNCTUATION, ')')) {
                do {
                    $arguments[]  = new IdentifierNode($stream->next()->getValue());
                    $currentToken = $stream->expect(Token::PUNCTUATION, [',', ')']);
                } while (!$currentToken->test(Token::PUNCTUATION, ')'));
            }
        } else {
            $arguments[] = new IdentifierNode($currentToken->getValue());
        }
        $stream->next();

        $stream->expectCurrent(Token::OPERATOR, '->');
        $stream->next();

        $parser->parse('expression');
        $body = $parser->popOperand();
    }
}