<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;

class Any extends Optional
{
    public function __construct(Parser $parser)
    {
        parent::__construct(new AtLeastOne($parser));
    }
}