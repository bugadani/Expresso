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

    public function __construct()
    {
        $this->accessOperator = new ArrayAccessOperator(0);
    }

    /**
     * @param TokenStream $stream
     * @param TokenStreamParser $parser
     */
    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $node = $parser->popOperand();

        $stream->next();
        $parser->parse('expression');

        $stream->expectCurrent(Token::PUNCTUATION, ']');
        $node = new BinaryOperatorNode($this->accessOperator, $node, $parser->popOperand());
        $parser->pushOperand($node);

        $stream->next();
        $parser->parse('postfix no function call');
    }
}