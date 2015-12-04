<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\TernaryOperatorNode;
use Expresso\Compiler\Operators\FunctionCallOperator;
use Expresso\Compiler\Parser;
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
        $parser->pushOperand(
            $operator->createNode(
                $parser->popOperand() //function name or filter/access node
            )
        );

        $stream->next();
        $parser->parse('argumentList');
    }
}