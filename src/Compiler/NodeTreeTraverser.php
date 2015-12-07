<?php

namespace Expresso\Compiler;

use Expresso\EvaluationContext;

class NodeTreeTraverser
{
    /**
     * @var NodeVisitorInterface[]
     */
    private $visitors;
    /**
     * @var EvaluationContext
     */
    private $context;

    public function __construct(EvaluationContext $context)
    {
        $this->visitors = [];
        $this->context  = $context;
    }

    public function addVisitor(NodeVisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }

    public function traverse(Node $nodeTree)
    {
        $firstStack  = new \SplStack();
        $secondStack = new \SplStack();

        $firstStack->push($nodeTree);
        $secondStack->push($nodeTree->getChildren());
        $this->enterNode($nodeTree);

        while (!$firstStack->isEmpty()) {
            $children = $secondStack->pop();
            if (empty($children)) {
                $node = $firstStack->pop();
                $this->leaveNode($node);
            } else {
                $node = array_pop($children);
                $secondStack->push($children);

                $firstStack->push($node);
                $secondStack->push($node->getChildren());
                $this->enterNode($node);
            }
        }

        $this->leaveNode($nodeTree);
    }

    private function leaveNode(Node $node)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->leaveNode($node);
        }
    }

    private function enterNode(Node $node)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->enterNode($node);
        }
    }
}