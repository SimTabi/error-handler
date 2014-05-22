<?php

namespace prgTW\ErrorHandler\Handler;

class HandlerManager
{
	/** @var \SplObjectStorage */
	protected $handlers;

	public function __construct()
	{
		$this->handlers = new \SplObjectStorage();
	}

	/**
	 * @param HandlerInterface $handler
	 * @param array            $categories
	 */
	public function attach(HandlerInterface $handler, array $categories = null)
	{
		if ($this->handlers->contains($handler))
		{
			$handlerCategories = $this->handlers->offsetGet($handler);
			$this->handlers->offsetSet($handler, array_merge((array)$handlerCategories, $categories));
		}
		else
		{
			$this->handlers->attach($handler, $categories);
		}
	}

	/**
	 * @param HandlerInterface $handler
	 */
	public function detach(HandlerInterface $handler)
	{
		$this->handlers->detach($handler);
	}

	/**
	 * @param array $categories
	 *
	 * @return HandlerInterface[]
	 */
	public function all(array $categories = array())
	{
		$handlers = array();

		foreach ($this->handlers as $handler)
		{
			$handlerCategories = $this->handlers->getInfo();
			if (array() === $categories || array_intersect($categories, $handlerCategories))
			{
				$handlers[] = $handler;
			}
		}

		return $handlers;
	}
}
