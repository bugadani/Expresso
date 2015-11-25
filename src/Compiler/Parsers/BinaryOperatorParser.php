<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class BinaryOperatorParser extends Parser
{
    /**
     * @var OperatorCollection
     */
    private $binaryOperators;

    public function __construct(OperatorCollection $binaryOperators)
    {
        $this->binaryOperators = $binaryOperators;
    }

    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        while ($this->binaryOperators->isOperator($currentToken->getValue())) {
            $parser->pushOperator(
                $this->binaryOperators->getOperator($currentToken->getValue())
            );
            $stream->next();
            $parser->parse('term');

            $currentToken = $stream->current();
        }
        $parser->popOperators();
    }
}