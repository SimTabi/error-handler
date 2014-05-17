<?php

namespace prgTW\ErrorHandler\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Metadata\Metadata;

interface HandlerInterface
{
	/**
	 * @param ErrorException $error
	 * @param Metadata       $metadata
	 */
	public function handleError(ErrorException $error, Metadata $metadata);

	/**
	 * @param \Exception $exception
	 * @param Metadata   $metadata
	 */
	public function handleException(\Exception $exception, Metadata $metadata);
}

