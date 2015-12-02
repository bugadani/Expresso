<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\VariableAccessNode;
use Expresso\Compiler\Operators\Binary\ArrayAccessOperator;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ArrayAccessParser extends Parser
{
    private $accessOperator;

    public function __construct()
    {
        $this->accessOperator = new ArrayAccessOperator(0);
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $node = $parser->popOperand();

        $stream->next();
        $parser->parse('expression');

        $stream->expectCurrent(Token::PUNCTUATION, ']');
        $node = new VariableAccessNode($this->accessOperator, $node, $parser->popOperand());
        $parser->pushOperand($node);

        $stream->next();
        $parser->parse('postfix no function call');
    }
}