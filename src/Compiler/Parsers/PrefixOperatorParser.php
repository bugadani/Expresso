<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Parser;
use Expresso\Compiler\SubParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;

class PrefixOperatorParser extends SubParser
{
    /**
     * @var OperatorCollection
     */
    private $prefixOperators;

    public function __construct(OperatorCollection $prefixOperators)
    {
        $this->prefixOperators = $prefixOperators;
    }

    public function parse(TokenStream $stream, Parser $parser)
    {
        $stream->expectCurrent(Token::OPERATOR, [$this->prefixOperators, 'isOperator']);
        $parser->pushOperator(
            $this->prefixOperators->getOperator($stream->consume()->getValue())
        );
        yield $parser->parse('term');
    }
}