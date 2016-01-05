<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\ParserSequence\Parsers\Sequence;
use Expresso\Compiler\ParserSequence\Parsers\TokenParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
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
        $grammar = new Sequence(
            [
                new TokenParser(Token::CONSTANT),
                new TokenParser(Token::EOF)
            ],
            function (array $children) {
                return $children;
            }
        );

        $result = GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));

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
        $grammar = new Sequence(
            [
                new TokenParser(Token::CONSTANT),
                new TokenParser(Token::EOF)
            ],
            function (array $children) {
                return $children;
            }
        );

        GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));
    }
}
