<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;

class RepeatAny extends Optional
{
    public function __construct(Parser $parser, callable $callback = null)
    {
        parent::__construct(new Repeat($parser), $callback);
    }

    public function emptyValue()
    {
        return [];
    }

    public function separatedBy(Parser $separator)
    {
        /** @var Repeat $parser */
        $parser = $this->getParser();
        $parser->separatedBy($separator);

        return $this;
    }
}