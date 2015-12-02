<?php

namespace Expresso\Extension\Lambda\Parsers;

use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extensions\Lambda\Nodes\LambdaNode;

class LambdaParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $arguments = [];
        if ($stream->nextTokenIf(Token::PUNCTUATION, '(')) {
            if (!$stream->nextTokenIf(Token::PUNCTUATION, ')')) {
                do {
                    $arguments[] = new IdentifierNode($stream->next()->getValue());
                    $stream->expect(Token::PUNCTUATION, [',', ')']);
                } while (!$stream->current()->test(Token::PUNCTUATION, ')'));
            }
        } else {
            $arguments[] = new IdentifierNode($stream->next()->getValue());
        }

        $stream->expect(Token::OPERATOR, '->');
        $stream->next();

        $parser->parse('expression');
        $body = $parser->popOperand();

        $parser->pushOperand(new LambdaNode($body, $arguments));
    }
}