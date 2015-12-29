<?php

namespace Expresso\Compiler\Utils;

class GeneratorHelper
{

    /**
     * This method allows recursive functions to be executed iteratively by using generators.
     * This is especially useful in PHP because the language imposes an artificial nesting limit.
     *
     * In order for a function to be executed, each recursive call must be yielded.
     *
     * @param \Generator $generator
     * @param callable|null $sendFunction
     */
    public static function executeGeneratorsRecursive(\Generator $generator, callable $sendFunction = null)
    {
        $stack = new \SplStack();
        $stack->push($generator);

        $done = false;
        //This is basically a simple iterative in-order tree traversal algorithm
        do {
            //get the current yielded value - this runs the generator if it is not initialized yet
            $yielded = $generator->current();

            //if it is a generator
            if ($yielded instanceof \Generator) {
                //... push it to the stack
                $stack->push($yielded);

                //... and mark it as the current one
                $generator = $yielded;
            } else {
                //at this point the current generator is done, remove it from the stack
                $stack->pop();

                //check if there are unfinished generators
                if ($stack->isEmpty()) {
                    //if not (the stack is empty), we're done
                    $done = true;
                } else {
                    //get the next generator from the stack
                    $generator = $stack->top();

                    //run the next generator
                    if ($sendFunction === null) {
                        $generator->next();
                    } else {
                        $generator->send($sendFunction($generator));
                    }
                }
            }
        } while (!$done);
    }
}