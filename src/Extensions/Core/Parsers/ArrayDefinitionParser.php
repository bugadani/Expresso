<?php

namespace Expresso\Extensions\Core\Parsers;

use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extensions\Core\Nodes\ListDataNode;
use Expresso\Extensions\Core\Nodes\MapDataNode;
use Expresso\Extensions\Core\Operators\Binary\RangeOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\InfiniteRangeOperator;

class ArrayDefinitionParser extends Parser
{

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        //Step to the first data token or closing bracket
        if ($stream->nextTokenIf(Token::PUNCTUATION, ']')) {
            //Empty array definition
            $array = new ListDataNode();
        } else {
            // Parse the first part in order to determine array type
            $stream->next();
            yield $parser->parse('expression');
            $data = $parser->popOperand();

            if ($this->isRangeOperator($data)) {
                //range operators may optionally have [] around them
                $array = $data;
            } else if ($stream->current()->test(Token::PUNCTUATION, [':', '=>'])) {
                //this is a map, so the previous value was a key
                $array = new MapDataNode();

                //retrieve first separator to disallow mixing them
                $separator = $stream->current()->getValue();

                //parse the first value expression
                $stream->next();
                yield $parser->parse('expression');
                $value = $parser->popOperand();

                $array->add($data, $value);

                //repeated optional part of multiple items
                while ($stream->current()->test(Token::PUNCTUATION, ',')) {

                    //parse the key
                    $stream->next();
                    yield $parser->parse('expression');
                    $key = $parser->popOperand();

                    $stream->expectCurrent(Token::PUNCTUATION, $separator);

                    //parse the value
                    $stream->next();
                    yield $parser->parse('expression');
                    $value = $parser->popOperand();

                    $array->add($key, $value);
                }
            } else {
                //simple list
                $stream->expectCurrent(Token::PUNCTUATION, ',');
                $array = new ListDataNode();
                $array->add($data);

                while ($stream->current()->test(Token::PUNCTUATION, ',')) {
                    $stream->next();
                    yield $parser->parse('expression');
                    $array->add($parser->popOperand());
                }
            }
            $stream->expectCurrent(Token::PUNCTUATION, ']');
        }

        //push array node to operand stack
        $stream->next();
        $parser->pushOperand($array);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function isRangeOperator($value)
    {
        if (!$value instanceof OperatorNode) {
            return false;
        }

        return $value->isOperator(RangeOperator::class) || $value->isOperator(InfiniteRangeOperator::class);
    }
}