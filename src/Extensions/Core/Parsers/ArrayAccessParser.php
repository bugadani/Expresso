<?php

namespace Expresso\Extensions\Core\Parsers;

use Expresso\Compiler\Nodes\BinaryOperatorNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extensions\Core\Operators\Binary\ArrayAccessOperator;

class ArrayAccessParser extends Parser
{
    private $accessOperator;

    public function __construct(ArrayAccessOperator $operator)
    {
        $this->accessOperator = $operator;
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $parser->pushOperator($this->accessOperator);

        $stream->next();
        yield $parser->parse('expression');

        $stream->expectCurrent(Token::PUNCTUATION, ']');

        $stream->next();
        yield $parser->parse('postfix no function call');
    }
}