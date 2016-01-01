<?php

namespace Expresso\Compiler;

use Expresso\Compiler\Utils\GeneratorHelper;

class Compiler
{
    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    /**
     * @var string
     */
    private $source;

    /**
     * @var \SplStack
     */
    private $sourceStack;

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
        $this->source .= $string;

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

    public function addData($data)
    {
        if (is_int($data)) {
            $this->add($data);
        } else if (is_float($data)) {
            $old = setlocale(LC_NUMERIC, 0);
            if ($old) {
                setlocale(LC_NUMERIC, 'C');
                $this->add($data);
                setlocale(LC_NUMERIC, $old);
            } else {
                $this->add($data);
            }
        } else if (is_bool($data)) {
            $this->add($data ? 'true' : 'false');
        } else if ($data === null) {
            $this->add('null');
        } else {
            $this->compileString($data);
        }

        return $this;
    }

    public function addVariableAccess($variableName)
    {
        $this->add("\$context['{$variableName}']");

        return $this;
    }

    public function compileNode(Node $node)
    {
        $this->sourceStack->push($this->source);
        $this->source = '';

        return $node->compile($this);
    }

    public function compile(Node $rootNode)
    {
        $this->source      = '';
        $this->sourceStack = new \SplStack();

        $generator = $this->compileNode($rootNode);

        GeneratorHelper::executeGeneratorsRecursive(
            $generator,
            function () {
                $source       = $this->source;
                $this->source = $this->sourceStack->pop();
                $this->source .= $source;

                return $source;
            }
        );

        return $this->source;
    }
}