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
    const TYPE_INDETERMINATE = 0;
    const TYPE_LIST          = 1;
    const TYPE_MAP           = 2;


    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        //Step to the first data token or closing bracket
        if ($stream->nextTokenIf(Token::PUNCTUATION, ']')) {
            //Empty array definition
            $array = new ListDataNode();
        } else {
            $listType = self::TYPE_INDETERMINATE;
            do {
                //expressions are allowed as both array keys and values.
                $stream->next();
                yield $parser->parse('expression');
                $data = $parser->popOperand();

                if ($listType === self::TYPE_INDETERMINATE) {
                    if ($this->isRangeOperator($data)) {
                        $array = $data;
                        $stream->expectCurrent(Token::PUNCTUATION, ']');
                        break;
                    } else if ($stream->current()->test(Token::PUNCTUATION, [':', '=>'])) {
                        $listType = self::TYPE_MAP;
                        $array    = new MapDataNode();
                    } else {
                        $listType = self::TYPE_LIST;
                        $array    = new ListDataNode();
                    }
                }

                if ($listType === self::TYPE_MAP) {
                    $stream->expectCurrent(Token::PUNCTUATION, [':', '=>']);
                    //the previous value was a key

                    $stream->next();
                    yield $parser->parse('expression');
                    $value = $parser->popOperand();

                    $array->add($data, $value);
                } else {
                    $array->add($data);
                }

                //Elements are comma separated
                $token = $stream->expectCurrent(Token::PUNCTUATION, [',', ']']);
            } while ($token->test(Token::PUNCTUATION, ','));
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