<?php

namespace prgTW\ErrorHandler\Tests;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\ErrorHandler;
use prgTW\ErrorHandler\Utils\Utils;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
	/** @var ErrorHandler */
	protected $errorHandler;

	/**
	 * @dataProvider getConfigurations
	 */
	public function testRegister($error, $exception, $shutdown)
	{
		$this->assertEquals(false, $this->errorHandler->getErrorHandlerRegistered());
		$this->assertEquals(false, $this->errorHandler->getExceptionHandlerRegistered());
		$this->assertEquals(false, $this->errorHandler->getShutdownHandlerRegistered());

		$this->errorHandler->register($error, $exception, $shutdown);

		$this->assertEquals($error, $this->errorHandler->getErrorHandlerRegistered());
		$this->assertEquals($exception, $this->errorHandler->getExceptionHandlerRegistered());
		$this->assertEquals($shutdown, $this->errorHandler->getShutdownHandlerRegistered());

		$this->errorHandler->unregister();

		$this->assertEquals(false, $this->errorHandler->getErrorHandlerRegistered());
		$this->assertEquals(false, $this->errorHandler->getExceptionHandlerRegistered());
		$this->assertEquals(false, $this->errorHandler->getShutdownHandlerRegistered());
	}

	public function getConfigurations()
	{
		return array(
			array(false, false, false),
			array(false, false, true),
			array(false, true, false),
			array(false, true, true),
			array(true, false, false),
			array(true, false, true),
			array(true, true, false),
			array(true, true, true),
		);
	}

	public function testErrorHandler()
	{
		$this->assertEquals(0, $this->errorHandler->getStats()->getErrorsHandled());
		$this->errorHandler->register(true, false, false);
		trigger_error('test', E_USER_ERROR);
		$this->errorHandler->unregister();
		$this->assertEquals(1, $this->errorHandler->getStats()->getErrorsHandled());
	}

	public function testExceptionHandler()
	{
		$this->assertEquals(0, $this->errorHandler->getStats()->getExceptionsHandled());
		$this->errorHandler->register(false, true, false);
		$this->errorHandler->handleException(new \Exception('test'));
		$this->errorHandler->unregister();
		$this->assertEquals(1, $this->errorHandler->getStats()->getExceptionsHandled());
	}

	public function testEventHandler()
	{
		$this->assertEquals(0, $this->errorHandler->getStats()->getEventsHandled());
		$this->errorHandler->handleEvent('event');
		$this->assertEquals(1, $this->errorHandler->getStats()->getEventsHandled());
	}

	/**
	 * @dataProvider getErrorTypes
	 */
	public function testIsCatchable($type, $isCatchable)
	{
		$ret = Utils::isCatchableOnShutdown(array('type' => $type));
		$this->assertEquals($isCatchable, $ret);
	}

	public function getErrorTypes()
	{
		return array(
			array(E_NOTICE, false),
			array(E_WARNING, false),
			array(E_ERROR, true),
			array(E_PARSE, true),
			array(E_CORE_ERROR, true),
			array(E_CORE_WARNING, false),
			array(E_COMPILE_ERROR, true),
			array(E_COMPILE_WARNING, false),
			array(E_USER_ERROR, true),
			array(E_USER_WARNING, false),
			array(E_USER_NOTICE, false),
			array(E_STRICT, false),
			array(E_RECOVERABLE_ERROR, true),
			array(E_DEPRECATED, false),
			array(E_USER_DEPRECATED, false),
		);
	}

	public function setUp()
	{
		$this->errorHandler = new ErrorHandler();
	}
}
