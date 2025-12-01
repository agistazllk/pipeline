<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Calculator.php';

class CalculatorTest extends TestCase
{
    public function testAdd()
    {
        $calc = new Calculator();
        $this->assertEquals(10, $calc->add(6, 4));
    }

    public function testSubtract()
    {
        $calc = new Calculator();
        $this->assertEquals(2, $calc->subtract(6, 4));
    }

    public function testMultiply()
    {
        $calc = new Calculator();
        $this->assertEquals(24, $calc->multiply(6, 4));
    }

    public function testDivide()
    {
        $calc = new Calculator();
        $this->assertEquals(1.5, $calc->divide(6, 4));
    }

    public function testDivideByZero()
    {
        $calc = new Calculator();
        $this->expectException(InvalidArgumentException::class);
        $calc->divide(6, 0);
    }
}