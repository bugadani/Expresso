<?php

namespace Expresso\Compiler\NodeVisitors;

use Expresso\Compiler\Node;
use Expresso\Compiler\NodeVisitorInterface;

class EvaluatingVisitor implements NodeVisitorInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var \SplStack
     */
    private $nodeStack;

    /**
     * @var \SplStack
     */
    private $resultStack;

    public function __construct(callable $callback)
    {
        $this->callback    = $callback;
        $this->nodeStack   = new \SplStack();
        $this->resultStack = new \SplStack();
    }

    public function enterNode(Node $node)
    {
        $this->nodeStack->push($node);
        $this->resultStack->push([]);
    }

    public function leaveNode(Node $node)
    {
        $node              = $this->nodeStack->pop();
        $evaluatedChildren = $this->resultStack->pop();

        $result = $node->evaluate($evaluatedChildren);

        if ($this->resultStack->isEmpty()) {
            call_user_func($this->callback, $result);
        } else {
            $parentEvaluated   = $this->resultStack->pop();
            $parentEvaluated[] = $result;
            $this->resultStack->push($parentEvaluated);
        }
    }
}