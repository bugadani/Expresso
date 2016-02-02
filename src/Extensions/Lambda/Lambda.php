<?php

namespace Expresso\Extensions\Lambda;

use Expresso\Compiler\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Parser\OperatorParser;
use Expresso\Compiler\Parser\Parsers\ParserReference;
use Expresso\Compiler\Parser\Parsers\TokenParser;
use Expresso\Compiler\Tokenizer\Token;
use Expresso\Extension;
use Expresso\Extensions\Core\Core;
use Expresso\Extensions\Lambda\Nodes\LambdaNode;

/**
 * Class Lambda defines the syntax of lambda expressions.
 *
 * Current EBNF:
 * lambda       = '\' , arguments , '->' , expression
 * arguments    = '()' | argument | '(' , argument , {', ' , argument} , ')'
 *
 * @package Expresso\Extensions\Lambda
 */
class Lambda extends Extension
{
    /**
     * @inheritdoc
     */
    public function getSymbols()
    {
        return ['->', '(', ')', '\\'];
    }

    /**
     * @inheritdoc
     */
    public function addParsers(OperatorParser $parser, CompilerConfiguration $configuration)
    {
        $parserContainer = $parser->getParserContainer();

        $expression = $parserContainer->get('expression');

        $reference = function ($name) use ($parserContainer) {
            return new ParserReference($parserContainer, $name);
        };

        $argumentName       = TokenParser::create(Token::IDENTIFIER);
        $comma              = TokenParser::create(Token::SYMBOL, ',');
        $openingParenthesis = TokenParser::create(Token::SYMBOL, '(');
        $closingParenthesis = TokenParser::create(Token::SYMBOL, ')');
        $arrowOperator      = TokenParser::create(Token::SYMBOL, '->');
        $lambdaPrefix       = TokenParser::create(Token::SYMBOL, '\\');
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            Core::class
        ];
    }
}

/**
 * This function tells if any element of the given collection satisfies a condition that is given in the callback.
 *
 * @param          $collection
 * @param callable $callback
 *
 * @return bool
 */
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


/**
 * This function tells if all of the elements in the given collection satisfy a condition that is given in the callback.
 *
 * @param          $collection
 * @param callable $callback
 *
 * @return bool
 */
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

/**
 * This function returns the elements of the given collection that satisfy a condition given in the callback.
 *
 * @param          $collection
 * @param callable $callback
 *
 * @return bool
 */
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

/**
 * This function returns the first element in the given collection that satisfy a condition given in the callback.
 *
 * @param          $collection
 * @param callable $callback
 *
 * @return bool
 */
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