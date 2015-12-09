<?php

namespace Expresso\Extensions\Strings;

use Expresso\Compiler\ExpressionFunction;
use Expresso\Extension;
use Expresso\Extensions\Core\Core;
use Expresso\Extensions\Strings\Operators\Binary\ConcatenationOperator;

class Strings extends Extension
{
    /**
     * @return array
     */
    public function getDependencies()
    {
        return [
            Core::class
        ];
    }

    public function getBinaryOperators()
    {
        return [
            /*new ContainsOperator(8, Operator::NONE),
            new EndsOperator(8, Operator::NONE),
            new MatchesOperator(8, Operator::NONE),
            new NotContainsOperator(8, Operator::NONE),
            new NotEndsOperator(8, Operator::NONE),
            new NotMatchesOperator(8, Operator::NONE),
            new NotStartsOperator(8, Operator::NONE),
            new StartsOperator(8, Operator::NONE),
            //other
            new NullCoalescingOperator(1),*/
            new ConcatenationOperator(10)
        ];
    }

    public function getFunctions()
    {
        return [
            new ExpressionFunction('replace', __NAMESPACE__ . '\expression_function_replace')
        ];
    }
}

function expression_function_replace($string, $search, $replacement = null)
{
    if ($replacement === null) {
        if (!is_array($search)) {
            throw new \InvalidArgumentException(
                '$search must be an array if only two arguments are supplied to replace'
            );
        }
        return str_replace(array_keys($search), $search, $string);
    } else {
        return str_replace($search, $replacement, $string);
    }
}