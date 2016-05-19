<?php

namespace Expresso\Test;

use Expresso\Expresso;

abstract class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Expresso
     */
    private $expresso;

    abstract public function create();

    abstract public function getDirectory();

    public function setUp()
    {
        $this->expresso = $this->create();
    }

    public function getTests()
    {
        $directory = realpath($this->getDirectory());

        $iterator = new \CallbackFilterIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            function (\SplFileInfo $file) {
                return $file->getExtension() === 'test';
            }
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $parsed = $this->parseDescriptor($file);
            if (is_array($parsed)) {
                yield $file->getPathname() => $parsed;
            }
        }
    }

    private function getBlock($string, $block)
    {
        $matches = [];
        if (!preg_match("/^--{$block}--\n(.*?)(?:\n--(?:[A-Z]+)--|\\Z)/ms", $string, $matches)) {
            return false;
        }

        return $matches[1];
    }

    private function parseDescriptor($file)
    {
        $testDescriptor = file_get_contents($file);
        $file           = basename($file);

        $testDescriptor = strtr($testDescriptor, ["\r" => '']);

        $skip       = $this->getBlock($testDescriptor, 'SKIP');
        $test       = $this->getBlock($testDescriptor, 'TEST');
        $expression = $this->getBlock($testDescriptor, 'EXPRESSION');
        $expect     = $this->getBlock($testDescriptor, 'EXPECT');
        $exception  = $this->getBlock($testDescriptor, 'EXCEPTION');
        $data       = $this->getBlock($testDescriptor, 'DATA');

        if ($skip) {
            $skipped = true;
        } else {
            $skipped = false;
        }

        if (!$test) {
            throw new \RuntimeException("{$file} does not contain a TEST block");
        }
        if (!$expression) {
            throw new \RuntimeException("{$file} does not contain an EXPRESSION block");
        }
        if ($expect === false && $exception === false) {
            throw new \RuntimeException("{$file} does not contain a EXPECT or EXCEPTION block");
        }
        if ($data) {
            eval('$data = [' . $data . '];');
        } else {
            $data = [];
        }

        $exceptionMessage = null;
        if ($exception && strpos($exception, "\n")) {
            list($exception, $exceptionMessage) = explode("\n", $exception, 2);
        }

        return [
            $file,
            $test,
            $expression,
            $data,
            $skipped,
            $expect,
            $exception,
            $exceptionMessage
        ];
    }

    /**
     * @test
     * @dataProvider getTests
     */
    public function runInterpreted(
        $file,
        $description,
        $expression,
        $data,
        $skipped,
        $expectation,
        $exception,
        $exceptionMessage
    )
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        if ($exception) {
            $this->expectException($exception);
            if (!empty($exceptionMessage)) {
                $this->expectExceptionMessage($exceptionMessage);
            }
        }

        $result = $this->expresso->execute($expression, $data);
        if ($expectation !== false) {
            $this->assertEquals(
                $expectation,
                $result,
                $description . ' (' . $file . ')'
            );
        }
    }

    /**
     * @test
     * @dataProvider getTests
     */
    public function runCompiled(
        $file,
        $description,
        $expression,
        $data,
        $skipped,
        $expectation,
        $exception,
        $exceptionMessage
    )
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        if ($exception) {
            $this->expectException($exception);
            if (!empty($exceptionMessage)) {
                $this->expectExceptionMessage($exceptionMessage);
            }
        }

        $compiled = $this->expresso->compile($expression);
        $result   = $compiled($data);
        if ($expectation !== false) {
            $this->assertEquals(
                $expectation,
                $result,
                $description . ' (' . $file . ', compiled)'
            );
        }
    }
}
