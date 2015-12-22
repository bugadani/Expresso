<?php

namespace Expresso\Test\Compiler;

use Expresso\Compiler\Token;
use Expresso\Compiler\Tokenizer;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testLineNumbers()
    {
        $tokenizer = new Tokenizer(['+', '-']);

        $stream = $tokenizer->tokenize("a + b - c\n  d");

        $expectedArray = [
            [
                'type'   => Token::IDENTIFIER,
                'value'  => 'a',
                'line'   => 1,
                'offset' => 0
            ],
            [
                'type'   => Token::OPERATOR,
                'value'  => '+',
                'line'   => 1,
                'offset' => 2
            ],
            [
                'type'   => Token::IDENTIFIER,
                'value'  => 'b',
                'line'   => 1,
                'offset' => 4
            ],
            [
                'type'   => Token::OPERATOR,
                'value'  => '-',
                'line'   => 1,
                'offset' => 6
            ],
            [
                'type'   => Token::IDENTIFIER,
                'value'  => 'c',
                'line'   => 1,
                'offset' => 8
            ],
            [
                'type'   => Token::IDENTIFIER,
                'value'  => 'd',
                'line'   => 2,
                'offset' => 2
            ],
            [
                'type'   => Token::EOF,
                'value'  => null,
                'line'   => 2,
                'offset' => 3
            ]
        ];
        foreach ($expectedArray as $expected) {
            $stream->next();
            $this->assertEquals($expected['type'], $stream->current()->getType());
            $this->assertEquals($expected['value'], $stream->current()->getValue());
            $this->assertEquals($expected['line'], $stream->current()->getLine());
            $this->assertEquals($expected['offset'], $stream->current()->getOffset());
        }
    }
}
