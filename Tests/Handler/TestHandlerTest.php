<?php

namespace prgTW\ErrorHandler\Tests\Handler;

use prgTW\ErrorHandler\ErrorHandler;

class TestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/** @var array */
	protected static $testConfiguration = array(
		'option' => 'value',
	);

	/** @var TestHandler */
	protected $testHandler;

	/** @var ErrorHandler */
	protected $errorHandler;

	public function testConfigurationPassed()
	{
		$this->assertEquals(self::$testConfiguration, $this->testHandler->getConfiguration());
	}

	public function testTestHandlerConnected()
	{
		$handlers = $this->errorHandler->getHandlers();
		$this->assertCount(1, $handlers);
		$this->assertEquals($this->testHandler, $handlers[0]);
	}

	/**
	 * @dataProvider getConfigurations
	 */
	public function testTestHandler($error, $exception)
	{
		$this->errorHandler->register($error, $exception);
	}

	public function getConfigurations()
	{
		return array(
			array(false, false),
			array(false, true),
			array(true, false),
			array(true, true),
		);
	}

	public function setUp()
	{
		$this->testHandler  = new TestHandler(self::$testConfiguration);
		$this->errorHandler = new ErrorHandler();
		$this->errorHandler->addHandler($this->testHandler);
	}

	public function tearDown()
	{
		$this->errorHandler->unregister();
	}
}
