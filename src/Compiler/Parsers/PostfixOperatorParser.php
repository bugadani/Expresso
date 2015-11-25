<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\OperatorCollection;
use Expresso\Compiler\Operators\UnaryOperator;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
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

    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        if ($this->postfixOperators->isOperator($currentToken->getValue())) {
            /** @var UnaryOperator $operator */
            $operator = $this->postfixOperators->getOperator($currentToken->getValue());
            $parser->popOperatorCompared($operator);

            $parser->pushOperand(//todo push operator?
                $operator->createNode(
                    $parser->popOperand()
                )
            );
            $stream->next();
        }
    }
}