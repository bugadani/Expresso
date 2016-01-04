<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Alternative extends Parser
{

    public static function create(array $parsers)
    {
        if (empty($parsers)) {
            throw new \InvalidArgumentException('$parsers must not be empty');
        }
        $alternative = new Alternative();
        foreach ($parsers as $parser) {
            $alternative->addOption($parser);
        }

        return $alternative;
    }

    /**
     * @var Parser[]
     */
    private $parsers = [];

    /**
     * @var Parser
     */
    private $activeParser;

    public function canParse(TokenStream $stream)
    {
        $this->activeParser = null;
        foreach ($this->parsers as $parser) {
            $canParse = (yield $parser->canParse($stream));
            if ($canParse) {
                $this->activeParser = $parser;

                break;
            }
        }

        yield ($this->activeParser !== null);
    }

    public function parse(TokenStream $stream)
    {
        $activeParser = $this->activeParser;
        if ($activeParser === null) {
            throw new \BadMethodCallException("This parser can not parse the current token");
        }
        $this->activeParser = null;
        yield $activeParser->parse($stream);
    }

    private function addOption(Parser $parser)
    {
        $this->parsers[] = $parser;
    }
}