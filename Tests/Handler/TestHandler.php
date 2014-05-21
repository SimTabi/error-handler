<?php

namespace prgTW\ErrorHandler\Tests\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Handler\HandlerInterface;
use prgTW\ErrorHandler\Metadata\Metadata;

class TestHandler implements HandlerInterface
{
	/** @var bool */
	protected $exceptionHandled = false;

	/** @var bool */
	protected $errorHandled = false;

	/** @var bool */
	protected $eventHandled = false;

	/** @var array */
	protected $context;

	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata = null)
	{
		$this->errorHandled = true;
		$this->context = $error->getContext();
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata = null)
	{
		$this->exceptionHandled = true;
	}

	/** {@inheritdoc} */
	public function handleEvent($event, Metadata $metadata = null)
	{
		$this->eventHandled = true;
	}

	/**
	 * @return boolean
	 */
	public function getErrorHandled()
	{
		return $this->errorHandled;
	}

	/**
	 * @return boolean
	 */
	public function getExceptionHandled()
	{
		return $this->exceptionHandled;
	}

	/**
	 * @return boolean
	 */
	public function getEventHandled()
	{
		return $this->eventHandled;
	}

	/**
	 * @return array
	 */
	public function getContext()
	{
		return $this->context;
	}
}
