<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\CompilerConfiguration;
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
    private $config;

    public function __construct(CompilerConfiguration $config)
    {
        $this->config              = $config;
        $this->conditionalOperator = $config->getOperatorByClass(ConditionalOperator::class);
    }

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        if ($stream->current()->test(Token::PUNCTUATION, '?')) {
            $stream->next();
            yield $parser->parse('expression');
            $stream->expectCurrent(Token::PUNCTUATION, ':');
            $stream->next();
            yield $parser->parse('expression');

            $right  = $parser->popOperand();
            $middle = $parser->popOperand();
            $left   = $parser->popOperand();

            $parser->pushOperand(
                $this->conditionalOperator->createNode($this->config, $left, $middle, $right)
            );
        }
    }
}