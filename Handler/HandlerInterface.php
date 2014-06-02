<?php

namespace prgTW\ErrorHandler\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Metadata\Metadata;

interface HandlerInterface
{
	/**
	 * @param ErrorException $error
	 * @param Metadata       $metadata
	 *
	 * @return null|string event ID
	 */
	public function handleError(ErrorException $error, Metadata $metadata);

	/**
	 * @param \Exception $exception
	 * @param Metadata   $metadata
	 *
	 * @return null|string event ID
	 */
	public function handleException(\Exception $exception, Metadata $metadata);

	/**
	 * @param string   $event
	 * @param Metadata $metadata
	 *
	 * @return null|string event ID
	 */
	public function handleEvent($event, Metadata $metadata);
}

