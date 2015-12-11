<?php

namespace Expresso\Extensions\Lambda\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extensions\Lambda\Nodes\LambdaNode;

class LambdaParser extends Parser
{

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $arguments = [];
        if ($stream->nextTokenIf(Token::PUNCTUATION, '(')) {
            if (!$stream->nextTokenIf(Token::PUNCTUATION, ')')) {
                do {
                    $arguments[] = $stream->next()->getValue();
                    $stream->expect(Token::PUNCTUATION, [',', ')']);
                } while (!$stream->current()->test(Token::PUNCTUATION, ')'));
            }
        } else {
            $arguments[] = $stream->next()->getValue();
        }

        $stream->expect(Token::OPERATOR, '->');
        $stream->next();

        yield $parser->parse('expression');
        $body = $parser->popOperand();

        $parser->pushOperand(new LambdaNode($body, $arguments));
    }
}