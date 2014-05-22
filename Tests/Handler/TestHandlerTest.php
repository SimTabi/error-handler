<?php

namespace prgTW\ErrorHandler\Tests\Handler;

use prgTW\ErrorHandler\ErrorHandler;

class TestHandlerTest extends \PHPUnit_Framework_TestCase
{
	/** @var TestHandler */
	protected $testHandler;

	/** @var ErrorHandler */
	protected $errorHandler;

	public function testGlobalHandlerConnected()
	{
		$handlers = $this->errorHandler->getHandlerManager()->all();
		$this->assertCount(1, $handlers);
		$this->assertEquals($this->testHandler, $handlers[0]);
	}

	public function testCategorization()
	{
		$handlerManager = $this->errorHandler->getHandlerManager();

		$handlerManager->attach($this->testHandler, array('category1'));
		$handlerManager->attach($this->testHandler, array('category1'));
		$handlerManager->attach($this->testHandler, array('category2'));
		$this->assertCount(1, $handlerManager->all());
		$this->assertCount(1, $handlerManager->all(array('category1')));
		$this->assertCount(1, $handlerManager->all(array('category2')));
		$this->assertCount(0, $handlerManager->all(array('non-existent')));
	}

	public function testTestHandlerHandlesError()
	{
		$context = array('option' => 'value');
		$this->assertFalse($this->testHandler->getErrorHandled());
		$this->errorHandler->handleError(E_USER_ERROR, 'message', __FILE__, __LINE__, $context);
		$this->assertTrue($this->testHandler->getErrorHandled());
		$this->assertEquals($context, $this->testHandler->getContext());
	}

	public function testTestHandlerHandlesException()
	{
		$this->assertFalse($this->testHandler->getExceptionHandled());
		$this->errorHandler->handleException(new \Exception());
		$this->assertTrue($this->testHandler->getExceptionHandled());
	}

	public function testTestHandlerHandlesEvent()
	{
		$this->assertFalse($this->testHandler->getEventHandled());
		$this->errorHandler->handleEvent('eventName');
		$this->assertTrue($this->testHandler->getEventHandled());
	}

	public function setUp()
	{
		$this->testHandler  = new TestHandler();
		$this->errorHandler = new ErrorHandler();
		$this->errorHandler->getHandlerManager()->attach($this->testHandler);
	}

	public function tearDown()
	{
		$this->errorHandler->unregister();
	}
}
