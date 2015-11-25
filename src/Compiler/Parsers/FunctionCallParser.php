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

    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $operator = $this->functionOperator;
        $parser->popOperatorCompared($operator);

        //either identifier or access node
        $node = $parser->popOperand();

        $stream->next();
        $parser->parse('argumentList');

        $parser->pushOperand(
            $operator->createNode(
                $node, //function name or filter/access node
                $parser->popOperand() //arguments
            )
        );

        $stream->expectCurrent(Token::PUNCTUATION, ')');

        $stream->next();
        $parser->parse('postfix');
    }
}