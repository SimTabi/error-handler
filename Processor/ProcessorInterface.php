<?php

namespace prgTW\ErrorHandler\Processor;

use prgTW\ErrorHandler\Metadata\Metadata;

interface ProcessorInterface
{
	/**
	 * @param Metadata   $metadata
	 * @param \Exception $exception
	 */
	public function process(Metadata $metadata, \Exception $exception = null);
}
