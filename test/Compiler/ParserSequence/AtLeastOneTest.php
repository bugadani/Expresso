<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\ParserSequence\Parsers\AtLeastOne;
use Expresso\Compiler\ParserSequence\Parsers\TokenParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;

class AtLeastOneTest extends \PHPUnit_Framework_TestCase
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
        $grammar = new AtLeastOne(
            new TokenParser(Token::CONSTANT),
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
        $grammar = new AtLeastOne(
            new TokenParser(Token::CONSTANT),
            function (array $children) {
                return $children;
            }
        );

        GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));
    }
}