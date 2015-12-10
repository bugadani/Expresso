<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Operators\FunctionCallOperator;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class FunctionCallParser extends Parser
{
    /**
     * @var FunctionCallOperator
     */
    private $functionOperator;

    public function __construct(FunctionCallOperator $functionOperator)
    {
        $this->functionOperator = $functionOperator;
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $operator = $this->functionOperator;
        $parser->popOperatorsWithHigherPrecedence($operator);

        //either identifier or filter/access node
        $functionNode = $operator->createNode(
            $parser->popOperand() //function name or filter/access node
        );

        $stream->next();
        $currentToken = $stream->current();
        if (!$currentToken->test(Token::PUNCTUATION, ')')) {
            do {
                yield $parser->parse('expression');
                $functionNode->addArgument($parser->popOperand());
                $currentToken = $stream->expectCurrent(Token::PUNCTUATION, [',', ')']);
                $stream->next();
            } while (!$currentToken->test(Token::PUNCTUATION, ')'));
        } else {
            $stream->next();
        }
        $parser->pushOperand($functionNode);
    }
}