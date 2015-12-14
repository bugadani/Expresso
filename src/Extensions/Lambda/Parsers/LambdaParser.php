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

        $nextToken = $stream->next();
        //parenthesis: parameter list
        if ($nextToken->test(Token::PUNCTUATION, '(')) {
            //empty parenthesis: empty parameter list
            if (!$stream->nextTokenIf(Token::PUNCTUATION, ')')) {
                //parse comma separated list
                do {
                    $arguments[] = $stream->next()->getValue();
                    $token = $stream->expect(Token::PUNCTUATION, [',', ')']);
                } while (!$token->test(Token::PUNCTUATION, ')'));
            }
        } else {
            //no parenthesis: single parameter
            $arguments[] = $nextToken->getValue();
        }

        $stream->expect(Token::OPERATOR, '->');

        $stream->next();
        yield $parser->parse('expression');
        $body = $parser->popOperand();

        $parser->pushOperand(new LambdaNode($body, $arguments));
    }
}