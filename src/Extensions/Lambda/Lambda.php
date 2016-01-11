<?php

namespace Expresso\Extensions\Lambda;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Parser;
use Expresso\Compiler\ParserSequence\Parsers\ParserReference;
use Expresso\Compiler\ParserSequence\Parsers\TokenParser;
use Expresso\Compiler\Token;
use Expresso\Extension;
use Expresso\Extensions\Core\Core;
use Expresso\Extensions\Lambda\Nodes\LambdaNode;
use Expresso\Extensions\Lambda\Operators\Binary\LambdaOperator;

class Lambda extends Extension
{
    public function getBinaryOperators()
    {
        return [
            new LambdaOperator(0)
        ];
    }

    public function addParsers(Parser $parser, CompilerConfiguration $configuration)
    {
        $parserContainer = $parser->getParserContainer();

        $expression = $parserContainer->get('expression');

        $reference = function ($name) use ($parserContainer) {
            return new ParserReference($parserContainer, $name);
        };

        $argumentName       = TokenParser::create(Token::IDENTIFIER);
        $comma              = TokenParser::create(Token::PUNCTUATION, ',');
        $openingParenthesis = TokenParser::create(Token::PUNCTUATION, '(');
        $closingParenthesis = TokenParser::create(Token::PUNCTUATION, ')');
        $arrowOperator      = TokenParser::create(Token::OPERATOR, '->');
        $lambdaPrefix       = TokenParser::create(Token::PUNCTUATION, '\\');
        $singleArgument     = TokenParser::create(Token::IDENTIFIER);

        $argList = $openingParenthesis
            ->followedBy(
                $argumentName
                    ->repeatSeparatedBy($comma)
                    ->optional()
            )
            ->followedBy($closingParenthesis);

        $lambdaDefinition = $lambdaPrefix
            ->followedBy(
                $argList
                    ->orA($singleArgument)
            )
            ->followedBy($arrowOperator)
            ->followedBy($reference('expression'));

        $argumentName->process(
            function (Token $token) {
                return $token->getValue();
            }
        );
        $singleArgument->process(
            function (Token $token) {
                return [$token->getValue()];
            }
        );
        $lambdaDefinition->process(
            function (array $children) {
                return new LambdaNode($children[3], $children[1]);
            }
        );

        $argList->process(
            function (array $children) {
                if ($children[1] === null) {
                    return [];
                }

                return $children[1];
            }
        );

        $extendedExpression = $expression->orA($lambdaDefinition);

        $parserContainer->set('expression', $extendedExpression);
    }

    public function getFunctions()
    {
        return [
            new ExpressionFunction('all', __NAMESPACE__ . '\expression_function_all'),
            new ExpressionFunction('any', __NAMESPACE__ . '\expression_function_any'),
            new ExpressionFunction('filter', __NAMESPACE__ . '\expression_function_filter'),
            new ExpressionFunction('first', __NAMESPACE__ . '\expression_function_first'),
            new ExpressionFunction('fold', __NAMESPACE__ . '\expression_function_fold'),
            new ExpressionFunction('map', __NAMESPACE__ . '\expression_function_map')
        ];
    }

    public function getDependencies()
    {
        return [
            Core::class
        ];
    }
}

function expression_function_any($collection, callable $callback)
{
    if (!is_array($collection) && !$collection instanceof \Traversable) {
        throw new \InvalidArgumentException('$collection must be an array or Traversable object');
    }

    foreach ($collection as $key => $item) {
        if ($callback($item, $key)) {
            return true;
        }
    }

    return false;
}

function expression_function_all($collection, callable $callback)
{
    if (!is_array($collection) && !$collection instanceof \Traversable) {
        throw new \InvalidArgumentException('$collection must be an array or Traversable object');
    }

    foreach ($collection as $key => $item) {
        if (!$callback($item, $key)) {
            return false;
        }
    }

    return true;
}

function expression_function_filter($collection, callable $callback)
{
    if (!is_array($collection) && !$collection instanceof \Traversable) {
        throw new \InvalidArgumentException('$collection must be an array or Traversable object');
    }

    foreach ($collection as $key => $value) {
        if ($callback($value)) {
            yield $key => $value;
        }
    }
}

function expression_function_first($collection, callable $callback)
{
    if (!is_array($collection) && !$collection instanceof \Traversable) {
        throw new \InvalidArgumentException('$collection must be an array or Traversable object');
    }

    foreach ($collection as $key => $item) {
        if ($callback($item, $key)) {
            return $item;
        }
    }

    return null;
}

function expression_function_map($collection, callable $callback)
{
    if (!is_array($collection) && !$collection instanceof \Traversable) {
        throw new \InvalidArgumentException('$collection must be an array or Traversable object');
    }

    foreach ($collection as $key => $value) {
        yield $key => $callback($value);
    }
}

function expression_function_fold($collection, callable $callback, $acc)
{
    foreach ($collection as $a) {
        $acc = $callback($acc, $a);
    }

    return $acc;
}