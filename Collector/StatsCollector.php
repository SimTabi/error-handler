<?php

namespace prgTW\ErrorHandler\Collector;

class StatsCollector
{
	/** @var int */
	protected $errorsHandled = 0;

	/** @var int */
	protected $exceptionsHandled = 0;

	/** @var int */
	protected $eventsHandled = 0;

	/**
	 * @param int $count
	 *
	 * @return $this
	 */
	public function addError($count = 1)
	{
		$this->errorsHandled += $count;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getErrorsHandled()
	{
		return $this->errorsHandled;
	}

	/**
	 * @param int $count
	 *
	 * @return $this
	 */
	public function addEvent($count = 1)
	{
		$this->eventsHandled += $count;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getEventsHandled()
	{
		return $this->eventsHandled;
	}

	/**
	 * @param int $exceptionsHandled
	 *
	 * @return $this
	 */
	public function addException($count = 1)
	{
		$this->exceptionsHandled += $count;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getExceptionsHandled()
	{
		return $this->exceptionsHandled;
	}
}
