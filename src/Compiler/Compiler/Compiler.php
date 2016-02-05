<?php

namespace Expresso\Compiler\Compiler;

use Expresso\Compiler\Node;
use Expresso\Compiler\Utils\GeneratorHelper;

class Compiler
{
    /**
     * @var CompilerConfiguration
     */
    private $configuration;

    /**
     * @var CompilerContext
     */
    private $context;

    /**
     * @var \SplStack
     */
    private $contextStack;

    /**
     * @var int
     */
    private $tempVariableCount;

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
        $this->context->source .= $string;

        return $this;
    }

    public function addTempVariable($source)
    {
        $num = $this->tempVariableCount++;

        $tempVar = new TempVar($this, "\$tempVar_{$num}", $source);

        $this->context->statements[] = $tempVar;

        return $tempVar;
    }

    public function compileStatements()
    {
        $this->context->compileStatements($this);
    }

    public function compileString($string)
    {
        $string = strtr($string, ["'" => "\'"]);

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
        $this->pushContext();

        return $node->compile($this);
    }

    public function compile(Node $rootNode)
    {
        $this->contextStack      = new \SplStack();
        $this->tempVariableCount = 0;

        $generator = $this->compileNode($rootNode);

        GeneratorHelper::executeGeneratorsRecursive(
            $generator,
            [$this, 'popContext']
        );

        return $this->context->source;
    }

    /**
     * @return CompilerContext
     */
    public function getContext()
    {
        return $this->context;
    }

    public function pushContext()
    {
        $this->contextStack->push($this->context);
        $this->context = new CompilerContext($this->context);
    }

    public function popContext()
    {
        $context       = $this->context;
        $this->context = $this->contextStack->pop();

        return $context;
    }
}