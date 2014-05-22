<?php

namespace prgTW\ErrorHandler\Processor;

class ProcessorManager
{
	/** @var \SplObjectStorage */
	protected $processors;

	public function __construct()
	{
		$this->processors = new \SplObjectStorage();
	}

	/**
	 * @param ProcessorInterface $processor
	 */
	public function attach(ProcessorInterface $processor)
	{
		$this->processors->attach($processor);
	}

	/**
	 * @param ProcessorInterface $processor
	 */
	public function detach(ProcessorInterface $processor)
	{
		$this->processors->detach($processor);
	}

	/**
	 * @return ProcessorInterface[]
	 */
	public function all()
	{
		$processors = array();

		foreach ($this->processors as $processor)
		{
			$processors[] = $processor;
		}

		return $processors;
	}
}
