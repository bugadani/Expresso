<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Parser;
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

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        while ($this->binaryOperators->isOperator($stream->current()->getValue())) {
            $parser->pushOperator(
                $this->binaryOperators->getOperator($stream->current()->getValue())
            );
            $stream->next();
            $parser->parse('term');
        }
    }
}