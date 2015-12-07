<?php

namespace Expresso\Compiler\Parsers;

use Expresso\Compiler\Parser;
use Expresso\Compiler\TokenStream;
use Expresso\Compiler\TokenStreamParser;

class ExpressionParser extends Parser
{
    public function parse(TokenStream $stream, TokenStreamParser $parser)
    {
        $parser->inOperatorStack(
            function ($stream, TokenStreamParser $parser) {
                $parser->parse('term');
                $parser->parse('binary');
            }
        );

        if ($parser->hasParser('conditional')) {
            $parser->parse('conditional');
        }
    }
}