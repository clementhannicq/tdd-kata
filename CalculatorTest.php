<?php

include("Calculator.php");

/**
* Test cases for calculator
*/
class CalculatorTest extends PHPUnit_Framework_TestCase
{
	private $c;

	function setUp()
	{
		//Create calculator
		$this->c = new Calculator();
	}
	
	function testReturnZero()
	{
		//Test if return 0 when there is no arguments
		$this->assertEquals(0, $this->c->add(""));
	}

	function testOneNumber()
	{
		//Test if return 0 when there is no arguments
		$this->assertEquals(42, $this->c->add("42"));
	}

	function testTwoNumbers()
	{
		//Test if Calculator can sum properly
		$this->assertEquals(42, $this->c->add("34,8"));
	}

	function testSum()
	{
		//Test if can sum multiple numbers
		$this->assertEquals(42, $this->c->add("21,8,12,1"));
	}

	function testDelimiter()
	{
		//Uses \n as delimiter
		$this->assertEquals(42, $this->c->add("9\n23"));
	}

	function testCustomDelimiter()
	{
		//Use a custom delimiter: +
		$this->assertEquals(42, $this->c->add("//+\n21+21"));
	}

	function testMultipleCustomDelimiter()
	{
		//Use a custom delimiter: +
		$this->assertEquals(42, $this->c->add("//+%\n19+21%2"));
	}

	/**
     * @expectedException NegativeNumberException
     */
	function testErrorNegatives()
	{
		//Negatives numbers are not allowed
		$this->c->add("-54,96");
	}

	function testIgnoreBigNumbers()
	{
		//Numbers bigger than 1000 should be ignored
		$this->assertEquals(42, $this->c->add("1001,42"));
	}

	function testLongDelimiters()
	{
		//Numbers bigger than 1000 should be ignored
		$this->assertEquals(42, $this->c->add("//[+++]\n21+++21"));
	}

	function testMultipleLongDelimiters()
	{
		$this->assertEquals(42, $this->c->add("//[11][00][51]\n201120055115"));
	}
	function testMixedDelimiters()
	{
		$this->assertEquals(42, $this->c->add("//[11]6[00][51]\n86121120055115"));
	}
	function testLogResult()
	{
		//Create a Logger Spy
		$logger = new LoggerSpy();

		//Create a calculator with the loggerSpy
		$c = new Calculator($logger);
		$c->add("21,21");
		$this->assertEquals(42, $logger->getOutput());
	}
	function testSendNotificationOnException()
	{
		//Create a Logger Stub that throws an Exception when log is called
		$logger = $this->getMock("Logger");
		$logger->method("log")->will($this->throwException(new Exception));

		//Create a Webservice Mock that is expected to be called
		$webservice = $this->getMock("Webservice");
		$webservice->expects($this->once())->method('notify');

		//Creates a Calculator with the above Mock/Stub and run it
		$c = new Calculator($logger, $webservice);
		$c->add("21,21");
	}
}

class LoggerSpy extends Logger
{
	private $output;

	function log($input)
	{
		$this->output = $input;
	}

	function getOutput()
	{
		return $this->output;
	}
}