<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Parser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class PostfixOperatorParser extends Parser
{
    /**
     * @var OperatorCollection
     */
    private $postfixOperators;

    public function __construct(OperatorCollection $postfixOperators)
    {
        $this->postfixOperators = $postfixOperators;
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $currentSymbol = $stream->current()->getValue();
        if ($this->postfixOperators->isOperator($currentSymbol)) {
            $parser->pushOperator(
                $this->postfixOperators->getOperator($currentSymbol)
            );
            $stream->next();
        }
    }
}