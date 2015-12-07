<?php

namespace Expresso\Test;

use Expresso\Expresso;
use Expresso\Extensions\Arithmetic;
use Expresso\Extensions\Bitwise;
use Expresso\Extensions\Core;
use Expresso\Extensions\Generator;
use Expresso\Extensions\Lambda;
use Expresso\Extensions\Logical;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Expresso
     */
    private $expresso;

    public function setUp()
    {
        $this->expresso = new Expresso();
        $this->expresso->addExtension(new Core());
        $this->expresso->addExtension(new Arithmetic());
        $this->expresso->addExtension(new Bitwise());
        $this->expresso->addExtension(new Lambda());
        $this->expresso->addExtension(new Logical());
        $this->expresso->addExtension(new Generator());
    }

    public function getTests()
    {
        $directory = realpath(__DIR__ . '/fixtures');

        $iterator = new \CallbackFilterIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            function (\SplFileInfo $file) {
                return $file->getExtension() === 'test';
            }
        );

        $tests = [];
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $tests[ $file->getPathname() ] = $this->parseDescriptor($file);
        }

        return array_filter(
            $tests,
            function ($descriptor) {
                //return $descriptor[1] === 'Test array access using both constant and variable keys';
                return is_array($descriptor);
            }
        );
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

        $testDescriptor = strtr(
            $testDescriptor,
            [
                "\r\n" => "\n",
                "\n\r" => "\n"
            ]
        );

        $skip       = $this->getBlock($testDescriptor, 'SKIP');
        $test       = $this->getBlock($testDescriptor, 'TEST');
        $expression = $this->getBlock($testDescriptor, 'EXPRESSION');
        $expect     = $this->getBlock($testDescriptor, 'EXPECT');
        $exception  = $this->getBlock($testDescriptor, 'EXCEPTION');
        $data       = $this->getBlock($testDescriptor, 'DATA');

        $exceptionMessage = null;

        if ($skip) {
            return false;
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

        if ($exception && strpos($exception, "\n")) {
            list($exception, $exceptionMessage) = explode("\n", $exception, 2);
        }

        return [
            $file,
            $test,
            $expression,
            $data,
            $expect,
            $exception,
            $exceptionMessage
        ];
    }

    /**
     * @test
     * @dataProvider getTests
     */
    public function runIntegrationTests(
        $file,
        $description,
        $expression,
        $data,
        $expectation,
        $exception,
        $exceptionMessage
    )
    {
        if ($data) {
            eval('$data = [' . $data . '];');
        } else {
            $data = [];
        }

        if ($exception) {
            $this->setExpectedException($exception, $exceptionMessage);
        }

        $return = $this->expresso->execute($expression, $data);
        if ($expectation) {
            $this->assertEquals(
                $expectation,
                $return,
                $description . ' (' . $file . ')'
            );

            $compiled = $this->expresso->compile($expression);
            $this->assertEquals(
                $expectation,
                $compiled($data),
                $description . ' (' . $file . ', compiled)'
            );
        }
    }
}