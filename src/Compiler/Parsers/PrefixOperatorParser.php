<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class PrefixOperatorParser extends Parser
{
    /**
     * @var OperatorCollection
     */
    private $prefixOperators;

    public function __construct(OperatorCollection $prefixOperators)
    {
        $this->prefixOperators = $prefixOperators;
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $current = $stream->expectCurrent(
            Token::OPERATOR,
            [$this->prefixOperators, 'isOperator']
        );
        $parser->pushOperator(
            $this->prefixOperators->getOperator($current->getValue())
        );
        $stream->next();
        yield $parser->parse('term');
    }
}