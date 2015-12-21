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

        if (!$stream->next()->test(Token::PUNCTUATION, ')')) {
            yield $parser->parse('expression');
            $functionNode->addArgument($parser->popOperand());
            while ($stream->current()->test(Token::PUNCTUATION, ',')) {
                $stream->next();
                yield $parser->parse('expression');
                $functionNode->addArgument($parser->popOperand());
            }
            $stream->expectCurrent(Token::PUNCTUATION, [')']);
        }
        $stream->next();

        $parser->pushOperand($functionNode);
    }
}