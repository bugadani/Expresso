<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\ParserSequence\Parsers\Alternative;
use Expresso\Compiler\ParserSequence\Parsers\TokenParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;

class AlternativeTest extends \PHPUnit_Framework_TestCase
{
    public function testGrammar()
    {
        $token          = new Token(Token::IDENTIFIER, 'a');
        $tokenGenerator = function () use ($token) {
            yield $token;
        };

        $stream = new TokenStream($tokenGenerator());

        $grammar = new Alternative(
            [
                new TokenParser(Token::IDENTIFIER, 'a'),
                new TokenParser(Token::IDENTIFIER, 'b')
            ]
        );

        $result = GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));
        $this->assertEquals($token, $result);
    }

    /**
     * @expectedException \Expresso\Compiler\Exceptions\SyntaxException
     */
    public function testNonMatchingGrammar()
    {
        $token          = new Token(Token::IDENTIFIER, 'c');
        $tokenGenerator = function () use ($token) {
            yield $token;
        };

        $stream = new TokenStream($tokenGenerator());

        $grammar = new Alternative(
            [
                new TokenParser(Token::IDENTIFIER, 'a'),
                new TokenParser(Token::IDENTIFIER, 'b')
            ]
        );

        GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));
    }
}
