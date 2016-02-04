<?php

namespace Expresso\Test;

use Expresso\Expresso;
use Expresso\Extensions\Core\Core;
use Expresso\Extensions\Generator\Generator;
use Expresso\Extensions\Lambda\Lambda;

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
        $this->expresso->addExtension(new Lambda());
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

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $parsed = $this->parseDescriptor($file);
            if (is_array($parsed) && $parsed[1] == 'Test nested generators') {
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

        $exceptionMessage = null;
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
    ) {
        if ($data) {
            eval('$data = [' . $data . '];');
        } else {
            $data = [];
        }

        if ($exception) {
            $this->setExpectedException($exception, $exceptionMessage);
        }

        $return = $this->expresso->execute($expression, $data);
        if ($expectation !== false) {
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