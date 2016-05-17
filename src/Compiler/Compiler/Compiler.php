<?php

namespace Expresso\Compiler\Compiler;

use Expresso\Compiler\Node;
use Recursor\Recursor;

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
    public function getConfiguration() : CompilerConfiguration
    {
        return $this->configuration;
    }

    public function add($string) : Compiler
    {
        $this->context->source .= $string;

        return $this;
    }

    public function requestTempVariable() : string
    {
        $num = $this->tempVariableCount++;

        return "\$tempVar_{$num}";
    }

    public function addTempVariable($source) : TempVar
    {
        if ($source instanceof TempVar) {
            return $source;
        }
        $tempVar = new TempVar($this, $this->requestTempVariable(), $source);

        $this->context->statements[] = $tempVar;

        return $tempVar;
    }

    public function addStatement($source) : Statement
    {
        $statement = new Statement($this, $source);

        $this->context->statements[] = $statement;

        return $statement;
    }

    public function compileStatements()
    {
        $this->context->compileStatements();
    }

    public function compileString($string) : Compiler
    {
        $string = strtr($string, ["'" => "\'"]);

        return $this->add("'{$string}'");
    }

    public function addData($data) : Compiler
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

    public function addVariableAccess($variableName) : Compiler
    {
        return $this->add("\$context['{$variableName}']");
    }

    public function compileNode(Node $node)
    {
        $this->pushContext();

        //This is a quasi-recursive call
        yield $node->compile($this);

        return $this->popContext();
    }

    public function compile(Node $rootNode) : string
    {
        $this->contextStack      = new \SplStack();
        $this->tempVariableCount = 0;

        $this->context = new CompilerContext();

        $generator = new Recursor([$this, 'compileNode']);

        return $generator($rootNode)->source;
    }

    public function pushContext()
    {
        $this->contextStack->push($this->context);
        $this->context = new CompilerContext();
    }

    public function popContext() : CompilerContext
    {
        $context       = $this->context;
        $this->context = $this->contextStack->pop();

        $this->context->statements = array_merge($this->context->statements, $context->statements);
        $context->statements       = [];

        return $context;
    }
}