<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;
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

        $grammar = TokenParser::create(Token::IDENTIFIER)
                              ->process(
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

        $grammar = TokenParser::create(Token::IDENTIFIER, 'b')
                              ->process(
                                  function (Token $child) use ($token) {
                                      $this->assertSame($token, $child);

                                      return 5;
                                  }
                              );

        GeneratorHelper::executeGeneratorsRecursive($grammar->parse($stream));
    }
}
