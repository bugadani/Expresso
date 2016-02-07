<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\Parser\Parsers\Optional;
use Expresso\Compiler\Parser\Parsers\Sequence;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;
use Recursor\Recursor;

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

        $grammar = Sequence::create(TokenParser::create(Token::CONSTANT, 'a'))
                           ->followedBy(new Optional(TokenParser::create(Token::CONSTANT, 'b')))
                           ->followedBy(TokenParser::create(Token::EOF));

        $result = new Recursor([$grammar, 'parse']);
        $this->assertEquals($tokens, $result($stream));
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

        $grammar = Sequence::create(TokenParser::create(Token::CONSTANT, 'a'))
                           ->followedBy(new Optional(TokenParser::create(Token::CONSTANT, 'b')))
                           ->followedBy(TokenParser::create(Token::EOF));

        $result = new Recursor([$grammar, 'parse']);

        $this->assertEquals([$tokens[0], null, $tokens[1]], $result($stream));
    }
}
