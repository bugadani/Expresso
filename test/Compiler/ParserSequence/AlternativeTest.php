<?php

namespace Expresso\Test\Compiler\ParserSequence;

use Expresso\Compiler\Parser\Parsers\Alternative;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Compiler\Tokenizer\TokenStream;
use Expresso\Compiler\Utils\GeneratorHelper;
use Recursor\Recursor;

class AlternativeTest extends \PHPUnit_Framework_TestCase
{
    public function testGrammar()
    {
        $token          = new Token(Token::IDENTIFIER, 'a');
        $tokenGenerator = function () use ($token) {
            yield $token;
        };

        $stream = new TokenStream($tokenGenerator());

        $grammar = Alternative::create(TokenParser::create(Token::IDENTIFIER, 'a'))
            ->orA(TokenParser::create(Token::IDENTIFIER, 'b'));

        $result = new Recursor([$grammar, 'parse']);
        $this->assertEquals($token, $result($stream));
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

        $grammar = Alternative::create(TokenParser::create(Token::IDENTIFIER, 'a'))
                              ->orA(TokenParser::create(Token::IDENTIFIER, 'b'));

        $result = new Recursor([$grammar, 'parse']);
        $result($stream);
    }
}
