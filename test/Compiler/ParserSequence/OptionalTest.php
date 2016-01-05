<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\ParserSequence\Parsers\Optional;
use Expresso\Compiler\ParserSequence\Parsers\Sequence;
use Expresso\Compiler\ParserSequence\Parsers\TokenParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;

class OptionalTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionalIsPresent()
    {
        $tokens = [
            new Token(Token::CONSTANT, 'a'),
            new Token(Token::CONSTANT, 'b'),
            new Token(Token::EOF)
        ];

        $tokenGenerator = function () use ($tokens) {
            foreach ($tokens as $token) {
                yield $token;
            }
        };

        $stream = new TokenStream($tokenGenerator());

        $grammar = new Sequence(
            [
                new TokenParser(Token::CONSTANT, 'a'),
                new Optional(new TokenParser(Token::CONSTANT, 'b')),
                new TokenParser(Token::EOF)
            ]
        );

        $result = GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));

        $this->assertEquals($tokens, $result);
    }

    public function testOptionalIsMissing()
    {
        $tokens = [
            new Token(Token::CONSTANT, 'a'),
            new Token(Token::EOF)
        ];

        $tokenGenerator = function () use ($tokens) {
            foreach ($tokens as $token) {
                yield $token;
            }
        };

        $stream = new TokenStream($tokenGenerator());

        $grammar = new Sequence(
            [
                new TokenParser(Token::CONSTANT, 'a'),
                new Optional(new TokenParser(Token::CONSTANT, 'b')),
                new TokenParser(Token::EOF)
            ]
        );

        $result = GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));

        $this->assertEquals([$tokens[0], null, $tokens[1]], $result);
    }
}
