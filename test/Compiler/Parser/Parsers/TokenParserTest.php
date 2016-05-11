<?php

namespace Expresso\Test\Compiler\Parser\Parsers;

use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;
use Recursor\Recursor;

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

        $result = new Recursor([$grammar, 'parse']);
        $this->assertEquals(5, $result($stream));
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

        $result = new Recursor([$grammar, 'parse']);
        $result($stream);
    }
}
