<?php

namespace Expresso\Compiler\ParserSequence\Parsers;

use Expresso\Compiler\Exceptions\SyntaxException;
use Expresso\Compiler\ParserSequence\Parser;
use Expresso\Compiler\TokenStream;

class Alternative extends Parser
{
    /**
     * @var Parser[]
     */
    private $parsers = [];

    /**
     * @var Parser
     */
    private $activeParser;

    public function __construct(array $parsers)
    {
        if (empty($parsers)) {
            throw new \InvalidArgumentException('$parsers must not be empty');
        }
        foreach ($parsers as $parser) {
            $this->addOption($parser);
        }
    }

    public function canParse(TokenStream $stream)
    {
        $this->activeParser = null;
        foreach ($this->parsers as $parser) {
            $canParse = (yield $parser->canParse($stream));
            if ($canParse) {
                $this->activeParser = $parser;

                yield true;
            }
        }

        yield false;
    }

    public function parse(TokenStream $stream)
    {
        $activeParser       = $this->activeParser;
        $this->activeParser = null;

        if ($activeParser === null) {
            foreach ($this->parsers as $parser) {
                if (yield $parser->canParse($stream)) {
                    $activeParser = $parser;
                    break;
                }
            }

            if ($activeParser === null) {
                throw new SyntaxException("This parser can not parse the current token {$stream->current()}");
            }
        }
        $child = (yield $activeParser->parse($stream));

        yield $this->emit($child);
    }

    private function addOption(Parser $parser)
    {
        $this->parsers[] = $parser;
    }
}