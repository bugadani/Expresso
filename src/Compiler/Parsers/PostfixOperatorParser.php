<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\SubParser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Parser;

class PostfixOperatorParser extends SubParser
{
    /**
     * @var OperatorCollection
     */
    private $postfixOperators;

    public function __construct(OperatorCollection $postfixOperators)
    {
        $this->postfixOperators = $postfixOperators;
    }

    public function parse(TokenStream $stream, Parser $parser)
    {
        $currentSymbol = $stream->current()->getValue();
        if ($this->postfixOperators->isOperator($currentSymbol)) {
            $parser->pushOperator(
                $this->postfixOperators->getOperator($currentSymbol)
            );
            $stream->next();
        }
        yield;
    }
}