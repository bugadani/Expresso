<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Nodes\DataNode;
use Expresso\Compiler\Nodes\FunctionCallNode;
use Expresso\Compiler\Nodes\IdentifierNode;
use Expresso\Compiler\Nodes\VariableAccessNode;
use Expresso\Compiler\Operators\Binary\ArrayAccessOperator;
use Expresso\Compiler\Operators\Binary\SimpleAccessOperator;
use Expresso\Compiler\Parser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class IdentifierTokenParser extends Parser
{
    public function parse(Token $currentToken, TokenStream $stream, TokenStreamParser $parser)
    {
        $identifier = $currentToken->getValue();

        //postfix 'outside' parsers, e.g. array access, function call
        if ($stream->nextTokenIf(Token::PUNCTUATION, '(')) {
            //function call
            $parser->parse('argumentList');
            $arguments = $parser->popOperand();

            $lastOperator = $parser->topOperator();
            if ($lastOperator instanceof SimpleAccessOperator) {
                $parser->popOperatorStack();
                $node = new MethodCallNode($parser->popOperand(), $identifier, $arguments);
            } else {
                $node       = new FunctionCallNode($identifier, $arguments);
            }
        } else {
            $node = new IdentifierNode($identifier);
            /** @var SimpleAccessOperator $simpleAccessOperator */
            $accessOperator = new ArrayAccessOperator(0);
            //array indexing
            while ($stream->nextTokenIf(Token::PUNCTUATION, '[')) {
                $parser->parseExpression();
                $stream->expectCurrent(Token::PUNCTUATION, ']');
                $node = new VariableAccessNode($accessOperator, $node, $parser->popOperand());
            }
        }
        $parser->pushOperand($node);
    }
}