<?php

namespace Expresso\Extensions\Core\Parsers;

use Expresso\Compiler\Nodes\ArrayDataNode;
use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\OperatorNode;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extensions\Core\Operators\Binary\RangeOperator;
use Expresso\Extensions\Core\Operators\Unary\Postfix\InfiniteRangeOperator;

class ArrayDefinitionParser extends Parser
{
    const TYPE_INDETERMINATE = 0;
    const TYPE_LIST = 1;
    const TYPE_MAP = 2;

    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $array    = new ArrayDataNode();
        $listType = self::TYPE_INDETERMINATE;

        //Step to the first data token or closing bracket
        $stream->next();
        while (!$stream->current()->test(Token::PUNCTUATION, ']')) {
            //expressions are allowed as both array keys and values.
            $parser->parse('expression');
            $value = $parser->popOperand();

            if ($listType === self::TYPE_INDETERMINATE) {
                if ($this->isRangeOperator($value)) {
                    $stream->expectCurrent(Token::PUNCTUATION, ']');
                    $array = $value;
                    break;
                } else if ($stream->current()->test(Token::PUNCTUATION, [':', '=>'])) {
                    $listType = self::TYPE_MAP;
                } else {
                    $key      = new DataNode(null);
                    $listType = self::TYPE_LIST;
                }
            }

            if ($listType === self::TYPE_MAP) {
                $stream->expectCurrent(Token::PUNCTUATION, [':', '=>']);
                //the previous value was a key
                $stream->next();
                $parser->parse('expression');
                $key   = $value;
                $value = $parser->popOperand();
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

    /**
     * @param $value
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