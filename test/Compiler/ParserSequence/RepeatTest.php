<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\Parser\Parsers\Repeat;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;
use Recursor\Recursor;

class RepeatTest extends \PHPUnit_Framework_TestCase
{
    public function testGrammar()
    {
        $tokens = [
            new Token(Token::CONSTANT, 1),
            new Token(Token::CONSTANT, 2),
            new Token(Token::CONSTANT, 3)
        ];

        $tokenGenerator = function () use ($tokens) {
            foreach ($tokens as $token) {
                yield $token;
            }

            yield new Token(Token::EOF);
        };

        $stream  = new TokenStream($tokenGenerator());
        $grammar = new Repeat(TokenParser::create(Token::CONSTANT));

        $generator = new Recursor([$grammar, 'parse']);
        $this->assertEquals($tokens, $generator($stream));
    }

    /**
     * @expectedException \Expresso\Compiler\Exceptions\SyntaxException
     */
    public function testNonMatchingGrammar()
    {
        $tokens = [
            new Token(Token::IDENTIFIER, 1),
            new Token(Token::CONSTANT, 2),
            new Token(Token::CONSTANT, 3)
        ];

        $tokenGenerator = function () use ($tokens) {
            foreach ($tokens as $token) {
                yield $token;
            }

            yield new Token(Token::EOF);
        };

        $stream  = new TokenStream($tokenGenerator());
        $grammar = new Repeat(TokenParser::create(Token::CONSTANT));

        $generator = new Recursor([$grammar, 'parse']);
        $generator($stream);
    }
}
