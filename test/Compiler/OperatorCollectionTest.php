<?php

namespace Expresso\Test\Compiler;

use Expresso\Compiler\Operator;
use Expresso\Compiler\OperatorCollection;

class OperatorCollectionTest extends \PHPUnit_Framework_TestCase
{
    private $mockOperator;

    /**
     * @var OperatorCollection
     */
    private $collection;
    private $otherOperator;

    public function setUp()
    {
        $this->mockOperator = $this->getMockBuilder(Operator::class)
            ->setConstructorArgs([1])
            ->getMockForAbstractClass();

        $this->otherOperator = $this->getMockBuilder(Operator::class)
            ->setConstructorArgs([1])
            ->getMockForAbstractClass();

        $this->collection = new OperatorCollection();

        $this->collection->addOperator('+', $this->mockOperator);
        $this->collection->addOperator('-', $this->mockOperator);
        $this->collection->addOperator('*', $this->otherOperator);
    }

    public function testEmptyCollection()
    {
        $collection = new OperatorCollection();

        $this->assertFalse($collection->isOperator('something'));
        $this->assertEmpty($collection->getSymbols());
    }

    public function testAllSymbolsAreAdded()
    {
        $this->assertEquals(['+', '-', '*'], $this->collection->getSymbols());

        $this->assertTrue($this->collection->isOperator('+'));
        $this->assertTrue($this->collection->isOperator('-'));
        $this->assertTrue($this->collection->isOperator('*'));
    }

    public function testOperatorsAreReturnedBySymbol()
    {
        $this->assertSame($this->mockOperator, $this->collection->getOperator('+'));
        $this->assertSame($this->mockOperator, $this->collection->getOperator('-'));
        $this->assertSame($this->otherOperator, $this->collection->getOperator('*'));
    }

}
