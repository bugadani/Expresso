<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extensions\Core\Operators\Ternary\ConditionalOperator;

class ConditionalParser extends Parser
{
    /**
     * @var ConditionalOperator
     */
    private $conditionalOperator;

    public function __construct(ConditionalOperator $operator)
    {
        $this->conditionalOperator = $operator;
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        if ($stream->current()->test(Token::PUNCTUATION, '?')) {
            $parser->pushOperator($this->conditionalOperator);

            $stream->next();
            yield $parser->parse('expression');

            $stream->expectCurrent(Token::PUNCTUATION, ':');

            $stream->next();
            yield $parser->parse('expression');
        }
    }
}