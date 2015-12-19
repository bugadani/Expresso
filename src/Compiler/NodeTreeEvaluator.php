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
        $stack = new \SplStack();
        $stack->push($node->evaluate($context));

        while (!$stack->isEmpty()) {
            //peek at the last generator
            $generator = $stack->top();

            //while it's not done
            while ($generator->valid()) {

                //get the next sub-generator ...
                $generator = $generator->current();
                while ($generator instanceof \Generator) {
                    //... and push it to the stack
                    $stack->push($generator);
                    //get the next sub-generator ...
                    $generator = $generator->current();
                }

                //at this point the last generator has no sub-generators, so remove it
                $stack->pop();

                if ($stack->isEmpty()) {
                    break;
                }
                //step the last generator that is not done
                $generator = $stack->top();
                $generator->send($context->getReturnValue());
            }
            if ($stack->isEmpty()) {
                break;
            }
            $stack->pop();
        }

        return $context->getReturnValue();

    }
}