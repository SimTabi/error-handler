<?php

namespace prgTW\ErrorHandler\Tests\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Handler\AbstractHandler;
use prgTW\ErrorHandler\Metadata\Metadata;

class TestHandler extends AbstractHandler
{
	/** @var bool */
	protected $exceptionHandled = false;

	/** @var bool */
	protected $errorHandled = false;

	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata)
	{
		$this->errorHandled = true;
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata)
	{
		$this->exceptionHandled = true;
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
}
