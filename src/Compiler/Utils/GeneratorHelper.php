<?php

namespace Expresso\Compiler\Utils;

class GeneratorHelper
{

    /**
     * This method allows recursive functions to be executed iteratively by using generators.
     * This is especially useful in PHP because the language imposes an artificial nesting limit.
     *
     * In order for a function to be executed, each recursive call must be yielded.
     * The last yield may be a scalar value; that value will be returned by this method.
     *
     * @param \Generator $generator
     * @param callable|null $sendFunction
     *
     * @return mixed
     */
    public static function executeGeneratorsRecursive(\Generator $generator, callable $sendFunction = null)
    {
        $stack = new \SplStack();
        $stack->push($generator);

        $done = false;
        //This is basically a simple iterative in-order tree traversal algorithm

        //Get the first yielded value
        $yielded = $generator->current();

        do {
            //If it is a generator
            while ($yielded instanceof \Generator) {
                //... push it to the stack
                $stack->push($yielded);

                //... run it, and mark it as the current active generator
                $yielded = $yielded->current();
            }

            //at this point the current generator is done (i.e. a non-generator was yielded), so remove it from the stack
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
                    $yielded = $generator->send($yielded);
                } else {
                    $yielded = $generator->send($sendFunction($generator, $yielded));
                }
            }
        } while (!$done);

        return $yielded;
    }
}