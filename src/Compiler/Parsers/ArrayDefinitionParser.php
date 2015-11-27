<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\ArrayDataNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ArrayDefinitionParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $array = new ArrayDataNode();

        //Step to the first data token or closing bracket
        $stream->next();
        while (!$stream->current()->test(Token::PUNCTUATION, ']')) {
            //expressions are allowed as both array keys and values.
            $parser->parse('expression');
            $value = $parser->popOperand();

            //Optional key support
            if ($stream->current()->test(Token::PUNCTUATION, [':', '=>'])) {
                //the previous value was a key
                $stream->next();
                $parser->parse('expression');
                $key   = $value;
                $value = $parser->popOperand();
            } else {
                $key = null;
            }

            $array->add($value, $key);
            //Elements are comma separated
            if ($stream->current()->test(Token::PUNCTUATION, ',')) {
                $stream->next();
            } else {
                $stream->expectCurrent(Token::PUNCTUATION, ']');
            }
        }
        //push array node to operand stack
        $stream->next();
        $parser->pushOperand($array);
    }
}