<?php

namespace Expresso\Extensions;

use Expresso\Compiler\CompilerConfiguration;
use Expresso\Compiler\ParserAlternativeCollection;
use Expresso\Compiler\Parsers\LambdaParser;
use Expresso\Compiler\Token;
use Expresso\Compiler\TokenStreamParser;
use Expresso\Extension;

class Lambda extends Extension
{

    public function getExtensionName()
    {
        return 'lambda';
    }

    public function addParsers(TokenStreamParser $parser, CompilerConfiguration $configuration)
    {
        $expressionParsers = $parser->getParser('expression');
        $expressionParsers = ParserAlternativeCollection::wrap($expressionParsers);
        $expressionParsers->addAlternative(new LambdaParser(), [Token::PUNCTUATION, '\\']);
    }
}