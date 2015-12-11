<?php

namespace Expresso\Compiler;

class Compiler
{
    /**
     * @var \SplQueue
     */
    private $taskQueue;

    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    public function __construct(CompilerConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return CompilerConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function add($string)
    {
        $this->taskQueue->enqueue($string);

        return $this;
    }

    public function compileString($string)
    {
        $string = strtr(
            $string,
            [
                "'"  => "\'",
                '\n' => "\n",
                '\t' => "\t"
            ]
        );

        return $this->add("'{$string}'");
    }

    private function compileArray($array)
    {
        $this->add('[');
        $separator = '';
        foreach ($array as $key => $value) {
            $this->add($separator);
            $separator = ', ';
            $this->addData($key);
            $this->add(' => ');
            $this->addData($value);
        }

        return $this->add(']');
    }

    public function addData($data)
    {
        if (is_int($data)) {
            $this->add($data);
        } elseif (is_float($data)) {
            $old = setlocale(LC_NUMERIC, 0);
            if ($old) {
                setlocale(LC_NUMERIC, 'C');
                $this->add($data);
                setlocale(LC_NUMERIC, $old);
            } else {
                $this->add($data);
            }
        } elseif (is_bool($data)) {
            $this->add($data ? 'true' : 'false');
        } elseif ($data === null) {
            $this->add('null');
        } elseif (is_array($data)) {
            $this->compileArray($data);
        } elseif ($data instanceof Node) {
            $this->compileNode($data);
        } else {
            $this->compileString($data);
        }

        return $this;
    }

    public function addVariableAccess($variableName)
    {
        $this->add('$context["' . $variableName . '"]');

        return $this;
    }

    public function compileNode(Node $node)
    {
        $this->taskQueue->enqueue($node);

        return $this;
    }

    private function setTaskQueue(\SplQueue $queue)
    {
        $oldQueue        = $this->taskQueue;
        $this->taskQueue = $queue;

        return $oldQueue;
    }

    public function compile(Node $rootNode)
    {
        $source          = '';
        $this->taskQueue = new \SplQueue();
        $this->taskQueue->setIteratorMode(\SplQueue::IT_MODE_DELETE);

        $this->compileNode($rootNode);

        //task queue based compilation ensures that the compilation
        //will never abort due to the nesting limit given by PHP
        while (!$this->taskQueue->isEmpty()) {
            $task = $this->taskQueue->dequeue();
            if ($task instanceof Node) {

                //replace queue with a new one
                $savedQueue = $this->setTaskQueue(new \SplQueue());

                $task->compile($this);

                //restore old queue
                $newQueue = $this->setTaskQueue($savedQueue);

                //prepend to old queue
                while (!$newQueue->isEmpty()) {
                    $this->taskQueue->unshift($newQueue->pop());
                }

            } else {
                $source .= $task;
            }
        }

        return $source;
    }
}