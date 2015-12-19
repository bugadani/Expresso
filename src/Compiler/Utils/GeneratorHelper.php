<?php

namespace Expresso\Compiler\Utils;

class GeneratorHelper
{
    public static function executeGeneratorsRecursive(\Generator $generator, callable $sendFunction = null)
    {
        $stack = new \SplStack();
        $stack->push($generator);

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
                if ($sendFunction !== null) {
                    $generator->send($sendFunction($generator));
                } else {
                    $generator->next();
                }
            }
            if (!$stack->isEmpty()) {
                $stack->pop();
            }
        }
    }
}