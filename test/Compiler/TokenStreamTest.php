<?php

namespace Expresso\Test\Compiler;

use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;

class TokenStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Expresso\Compiler\Exceptions\SyntaxException
     */
    public function testExpectTypeException()
    {
        $tokens = new \ArrayIterator(
            [
                new Token(Token::IDENTIFIER),
                new Token(Token::EOF)
            ]
        );

        $stream = new TokenStream($tokens);
        $stream->expect(Token::CONSTANT);
    }

    /**
     * @expectedException \Expresso\Compiler\Exceptions\SyntaxException
     */
    public function testExpectValueException()
    {
        $tokens = new \ArrayIterator(
            [
                new Token(Token::IDENTIFIER, 'foo'),
                new Token(Token::IDENTIFIER, 'baar'),
                new Token(Token::EOF)
            ]
        );

        $stream = new TokenStream($tokens);
        $stream->expect(Token::IDENTIFIER, 'bar');
    }

    public function testExpectStepsToNextToken()
    {
        $token1 = new Token(Token::IDENTIFIER, 'foo');
        $token2 = new Token(Token::IDENTIFIER, 'bar');

        $tokens = new \ArrayIterator(
            [
                $token1,
                $token2,
                new Token(Token::EOF)
            ]
        );

        $stream = new TokenStream($tokens);
        $this->assertSame($token1, $stream->expect(Token::IDENTIFIER, 'foo'));
    }
}
