<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\ArgumentListNode;
use Expresso\Compiler\Operators\FunctionCallOperator;
use Expresso\Compiler\SubParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Parser;

class FunctionCallParser extends SubParser
{
    /**
     * @var FunctionCallOperator
     */
    private $functionOperator;

    public function __construct(FunctionCallOperator $functionOperator)
    {
        $this->functionOperator = $functionOperator;
    }

    public function parse(TokenStream $stream, Parser $parser)
    {
        $argumentListNode = new ArgumentListNode();
        if (!$stream->next()->test(Token::PUNCTUATION, ')')) {

            yield $parser->parse('expression');
            $argumentListNode->addChild($parser->popOperand());

            while ($stream->current()->test(Token::PUNCTUATION, ',')) {
                $stream->next();

                yield $parser->parse('expression');
                $argumentListNode->addChild($parser->popOperand());
            }
            $stream->expectCurrent(Token::PUNCTUATION, [')']);
        }
        $stream->next();

        $parser->pushOperator($this->functionOperator);
        $parser->pushOperand($argumentListNode);
    }
}