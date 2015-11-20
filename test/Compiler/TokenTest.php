<?php

namespace Expresso\Test\Compiler;

use Expresso\Compiler\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{

    public function testGetters()
    {
        $token = new Token(Token::IDENTIFIER, 'foobar', 7);

        $this->assertNotEquals(Token::TEXT, $token->getType());
        $this->assertEquals(Token::IDENTIFIER, $token->getType());

        $this->assertNotEquals('foo', $token->getValue());
        $this->assertEquals('foobar', $token->getValue());
    }

    public function testSimpleTokenWithoutValue()
    {
        $token = new Token(Token::IDENTIFIER);
        $this->assertFalse($token->test(Token::LITERAL));

        $this->assertTrue($token->test(Token::IDENTIFIER));
        $this->assertFalse($token->test(Token::IDENTIFIER, 'apple'));
    }

    public function testSimpleTokenWithValue()
    {
        $token = new Token(Token::LITERAL, 4);

        $this->assertFalse($token->test(Token::TEXT));
        $this->assertFalse($token->test(Token::LITERAL, 3));
        $this->assertFalse($token->test(Token::LITERAL, [3, 5]));
        $this->assertFalse($token->test(Token::LITERAL, 'is_string'));

        $this->assertTrue($token->test(Token::LITERAL));
        $this->assertTrue($token->test(Token::LITERAL, 4));
        $this->assertTrue($token->test(Token::LITERAL, 'is_int'));
        $this->assertTrue($token->test(Token::LITERAL, [4, 5]));
    }
}
