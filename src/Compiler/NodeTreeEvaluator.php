<?php
namespace Expresso\Compiler;

use Expresso\Compiler\Utils\TreeHelper;
use Expresso\EvaluationContext;

class NodeTreeEvaluator
{
    /**
     * @var \SplStack
     */
    private $resultStack;

    /**
     * @var array
     */
    private $results;

    public function __construct()
    {
        $this->resultStack = new \SplStack();
        $this->results     = [];
    }

    public function evaluate(Node $node, EvaluationContext $context)
    {
        if ($node->hasData('noEvaluate')) {
            return null;
        }

        TreeHelper::traverse(
            $node,
            function (Node $node) {
                if ($node->hasData('noEvaluate')) {
                    return false;
                } else {
                    $this->resultStack->push($this->results);
                    $this->results = [];

                    return true;
                }
            },
            function (Node $node) use ($context) {
                $results         = $this->results;
                $this->results   = $this->resultStack->pop();
                $this->results[] = $node->evaluate($context, $results, $this);
            }
        );

        return array_pop($this->results);
    }
}