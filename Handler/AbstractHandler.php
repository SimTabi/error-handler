<?php

namespace prgTW\ErrorHandler\Handler;

abstract class AbstractHandler implements HandlerInterface
{
	/** @var array */
	protected $configuration;

	public function __construct(array $configuration)
	{
		$this->setConfiguration($configuration);
	}

	/**
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @param array $configuration
	 *
	 * @return $this
	 */
	protected function setConfiguration(array $configuration)
	{
		$this->configuration = $configuration;

		return $this;
	}
}
