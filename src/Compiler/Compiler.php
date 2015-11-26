<?php

namespace Expresso\Compiler;

class Compiler
{
    private $source;

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

    public function compileNode(NodeInterface $node)
    {
        $node->compile($this);

        return $this;
    }

    public function compile(NodeInterface $rootNode)
    {
        $this->source = '';
        $rootNode->compile($this);

        return $this->source;
    }
}