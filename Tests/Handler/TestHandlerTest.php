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

	public function testTestHandlerHandlesError()
	{
		$this->assertFalse($this->testHandler->getErrorHandled());
		$this->errorHandler->register(true, false);
		$this->errorHandler->handleError(E_USER_ERROR, 'message', __FILE__, __LINE__);
		$this->assertTrue($this->testHandler->getErrorHandled());
	}

	public function testTestHandlerHandlerException()
	{
		$this->assertFalse($this->testHandler->getExceptionHandled());
		$this->errorHandler->register(false, true);
		$this->errorHandler->handleException(new \Exception());
		$this->assertTrue($this->testHandler->getExceptionHandled());
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
