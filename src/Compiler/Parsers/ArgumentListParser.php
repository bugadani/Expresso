<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ArgumentListParser extends Parser
{
    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $currentToken = $stream->current();
        if (!$currentToken->test(Token::PUNCTUATION, ')')) {
            /** @var FunctionCallNode $node */
            $node = $parser->popOperand();
            while (!$currentToken->test(Token::PUNCTUATION, ')')) {
                $parser->parse('expression');
                $node->addArgument($parser->popOperand());
                $currentToken = $stream->expectCurrent(Token::PUNCTUATION, [',', ')']);
                $stream->next();
            }
            $parser->pushOperand($node);
        } else {
            $stream->next();
        }
    }
}