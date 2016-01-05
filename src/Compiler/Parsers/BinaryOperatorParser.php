<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\SubParser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Parser;

class BinaryOperatorParser extends SubParser
{
    /**
     * @var OperatorCollection
     */
    private $binaryOperators;

    public function __construct(OperatorCollection $binaryOperators)
    {
        $this->binaryOperators = $binaryOperators;
    }

    public function parse(TokenStream $stream, Parser $parser)
    {
        while ($this->binaryOperators->isOperator($stream->current()->getValue())) {
            $parser->pushOperator(
                $this->binaryOperators->getOperator($stream->consume()->getValue())
            );
            yield $parser->parse('term');
        }
    }
}