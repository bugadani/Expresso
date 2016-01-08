<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;

class RepeatAny extends Optional
{
    public function __construct(Parser $parser)
    {
        parent::__construct(new Repeat($parser));
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