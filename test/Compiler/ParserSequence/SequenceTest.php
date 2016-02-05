<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\Parser\Parsers\Sequence;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;

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

        $result = \Expresso\runQuasiRecursive($grammar->parse($stream));

        $this->assertEquals($tokens, $result);
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

        \Expresso\runQuasiRecursive($grammar->parse($stream));
    }
}
