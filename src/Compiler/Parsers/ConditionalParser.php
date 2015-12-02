<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Operators\Ternary\ConditionalOperator;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ConditionalParser extends Parser
{
    public function __construct()
    {
        $this->conditionalOperator = new ConditionalOperator(0);
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        if($stream->current()->test(Token::PUNCTUATION, '?')) {
            $stream->next();
            $parser->parse('expression');
            $stream->expectCurrent(Token::PUNCTUATION, ':');
            $stream->next();
            $parser->parse('expression');

            $right  = $parser->popOperand();
            $middle = $parser->popOperand();
            $left   = $parser->popOperand();

            $parser->pushOperand(
                $this->conditionalOperator->createNode($left, $middle, $right)
            );
        }
    }
}