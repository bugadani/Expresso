<?php

namespace Expresso\Test\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\Parsers\Sequence;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;
use Recursor\Recursor;

class SequenceTest extends \PHPUnit_Framework_TestCase
{
    public function testGrammar()
    {
        $tokens = [
            new Token(Token::CONSTANT, 1),
            new Token(Token::EOF)
        ];

        $tokenGenerator = function () use ($tokens) {
            foreach ($tokens as $token) {
                yield $token;
            }
        };

        $stream  = new TokenStream($tokenGenerator());
        $grammar = Sequence::create(TokenParser::create(Token::CONSTANT))
                           ->followedBy(TokenParser::create(Token::EOF));

        $result = new Recursor([$grammar, 'parse']);
        $this->assertEquals($tokens, $result($stream));
    }

    /**
     * @expectedException \Expresso\Compiler\Exceptions\SyntaxException
     */
    public function testNonMatchingGrammar()
    {
        $tokens = [
            new Token(Token::IDENTIFIER, 1),
            new Token(Token::EOF)
        ];

        $tokenGenerator = function () use ($tokens) {
            foreach ($tokens as $token) {
                yield $token;
            }
        };

        $stream  = new TokenStream($tokenGenerator());
        $grammar = Sequence::create(TokenParser::create(Token::CONSTANT))
                           ->followedBy(TokenParser::create(Token::EOF));

        $result = new Recursor([$grammar, 'parse']);
        $result($stream);
    }
}
