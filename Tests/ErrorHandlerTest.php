<?php

namespace prgTW\ErrorHandler\Tests;

use prgTW\ErrorHandler\Error\Severity;
use prgTW\ErrorHandler\ErrorHandler;
use prgTW\ErrorHandler\Metadata\Metadata;

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
	 * @dataProvider provideSeverityTranslations
	 */
	public function testSeverityTranslation($errorNo, $severity)
	{
		$this->assertEquals($severity, Severity::fromPhpErrorNo($errorNo));
	}

	public function provideSeverityTranslations()
	{
		return array(
			array(0, Severity::ERROR), //default
			array(E_NOTICE, Severity::NOTICE),
			array(E_WARNING, Severity::WARNING),
			array(E_ERROR, Severity::ERROR),
			array(E_PARSE, Severity::ERROR),
			array(E_CORE_ERROR, Severity::ERROR),
			array(E_CORE_WARNING, Severity::WARNING),
			array(E_COMPILE_ERROR, Severity::ERROR),
			array(E_COMPILE_WARNING, Severity::WARNING),
			array(E_USER_ERROR, Severity::ERROR),
			array(E_USER_WARNING, Severity::WARNING),
			array(E_USER_NOTICE, Severity::NOTICE),
			array(E_STRICT, Severity::INFO),
			array(E_RECOVERABLE_ERROR, Severity::ERROR),
			array(E_DEPRECATED, Severity::WARNING),
			array(E_USER_DEPRECATED, Severity::WARNING),
		);
	}

	public function testSkipping()
	{
		$this->assertEquals(0, $this->errorHandler->getStats()->getEventsHandled());
		$this->errorHandler->handleEvent('event', (new Metadata)->setAction(Metadata::ACTION_SKIP));
		$this->assertEquals(0, $this->errorHandler->getStats()->getEventsHandled());
	}

	public function setUp()
	{
		$this->errorHandler = new ErrorHandler();
	}
}
