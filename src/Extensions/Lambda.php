<?php

namespace Expresso\Extensions;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\ExpressionFunction;
use Expresso\Compiler\Operators\Binary\LambdaOperator;
use Expresso\Compiler\ParserAlternativeCollection;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extension;
use Expresso\Extension\Lambda\Parsers\LambdaParser;
use Expresso\Utils\TransformIterator;

class Lambda extends Extension
{
    public function getBinaryOperators()
    {
        return [
            new LambdaOperator(0)
        ];
    }

    public function addParsers(TokenStreamParser $parser, CompilerConfiguration $configuration)
    {
        $expressionParsers = $parser->getParser('expression');
        $expressionParsers = ParserAlternativeCollection::wrap($expressionParsers);
        $expressionParsers->addAlternative(new LambdaParser(), [Token::PUNCTUATION, '\\']);

        $parser->addParser('expression', $expressionParsers);
    }

    public function getFunctions()
    {
        return [
            new ExpressionFunction('fold', __NAMESPACE__ . '\expression_function_fold'),
            new ExpressionFunction('map', __NAMESPACE__ . '\expression_function_map')
        ];
    }

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\Core'
        ];
    }
}

function expression_function_map($collection, callable $callback)
{
    if (is_array($collection)) {
        $collection = new \ArrayIterator($collection);
    }

    return new TransformIterator($collection, $callback);
}

function expression_function_fold($collection, callable $callback, $initial)
{
    $acc = $initial;
    foreach ($collection as $a) {
        $acc = $callback($acc, $a);
    }

    return $acc;
}