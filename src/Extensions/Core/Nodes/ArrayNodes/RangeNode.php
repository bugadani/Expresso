<?php

namespace Expresso\Extensions\Core\Nodes\ArrayNodes;

use Expresso\Compiler\Compiler\Compiler;
use Expresso\Compiler\Node;
use Expresso\Runtime\ExecutionContext;

class RangeNode extends Node
{
    /**
     * @var Node
     */
    private $start;

    /**
     * @var Node
     */
    private $end;

    public function __construct(Node $start, Node $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function compile(Compiler $compiler)
    {
        if ($this->end === null) {
            $generator = $compiler->addTempVariable('function ($start) {
                while (true) {
                    yield $start++;
                }
            }');

            $start = yield $compiler->compileNode($this->start);
            $compiler->add("{$generator}({$start})");
        } else {
            $generator = $compiler->addTempVariable('
                function ($start, $end) {
                    if ($start < $end) {
                        while ($start <= $end) {
                            yield $start++;
                        }
                    } else {
                        while ($end <= $start) {
                            yield $start--;
                        }
                    }
                }');

            $start = yield $compiler->compileNode($this->start);
            $end   = yield $compiler->compileNode($this->end);
            $compiler->add("{$generator}({$start}, {$end})");
        }
    }

    public function evaluate(ExecutionContext $context)
    {
        $start = yield $this->start->evaluate($context);
        if ($this->end === null) {
            $generator = function ($start) {
                while (true) {
                    yield $start++;
                }
            };

            return $generator($start);
        } else {
            $end = yield $this->end->evaluate($context);
            if ($start < $end) {
                $generator = function ($start, $end) {
                    while ($start <= $end) {
                        yield $start++;
                    }
                };
            } else {
                $generator = function ($start, $end) {
                    while ($end <= $start) {
                        yield $start--;
                    }
                };
            }

            return $generator($start, $end);
        }
    }
}