<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\ParserSequence\Parsers\TokenParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;

class TokenParserTest extends \PHPUnit_Framework_TestCase
{
    public function testGrammar()
    {
        $token          = new Token(Token::IDENTIFIER, 'a');
        $tokenGenerator = function () use ($token) {
            yield $token;
        };

        $stream = new TokenStream($tokenGenerator());

        $grammar = new TokenParser(
            Token::IDENTIFIER,
            null,
            function (Token $child) use ($token) {
                $this->assertSame($token, $child);

                return 5;
            }
        );

        $result = GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));
        $this->assertEquals(5, $result);
    }

    /**
     * @expectedException \Expresso\Compiler\Exceptions\SyntaxException
     */
    public function testNonMatchingGrammar()
    {
        $token          = new Token(Token::IDENTIFIER, 'a');
        $tokenGenerator = function () use ($token) {
            yield $token;
        };

        $stream = new TokenStream($tokenGenerator());

        $grammar = new TokenParser(
            Token::IDENTIFIER,
            'b',
            function (Token $child) use ($token) {
                $this->assertSame($token, $child);

                return 5;
            }
        );

        GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));
    }
}
