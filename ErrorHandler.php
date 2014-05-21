<?php

namespace prgTW\ErrorHandler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Handler\HandlerInterface;
use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;

class ErrorHandler
{
	/** @var bool */
	protected $errorHandlerRegistered = false;

	/** @var bool */
	protected $exceptionHandlerRegistered = false;

	/** @var bool */
	protected $shutdownHandlerRegistered = false;

	/** @var int */
	protected $errorsHandled = 0;

	/** @var int */
	protected $exceptionsHandled = 0;

	/** @var int */
	protected $eventsHandled = 0;

	/** @var HandlerInterface[] */
	protected $handlers = array();

	/** @var array */
	protected $handlerCategories = array();

	/** @var ProcessorInterface[] */
	protected $processors = array();

	/**
	 * @param HandlerInterface $handler
	 * @param array            $categories
	 *
	 * @return $this
	 * @throws \LogicException when category is not of type "string"
	 */
	public function addHandler(HandlerInterface $handler, array $categories = array())
	{
		$categories            = $this->getCategories($categories);
		$hash                  = spl_object_hash($handler);
		$this->handlers[$hash] = $handler;
		if (isset($this->handlerCategories[$hash]))
		{
			$this->handlerCategories[$hash] = array_merge($this->handlerCategories[$hash], $categories);
		}
		else
		{
			$this->handlerCategories[$hash] = $this->getCategories($categories);
		}

		return $this;
	}

	/**
	 * @param array $categories
	 *
	 * @return HandlerInterface[]
	 * @throws \LogicException when category is not of type "string"
	 */
	public function getHandlers(array $categories = array())
	{
		$handlers   = $this->handlers;
		$categories = $this->getCategories($categories);
		if ($categories !== array())
		{
			foreach ($handlers as $hash => $handler)
			{
				if (!array_intersect($this->handlerCategories[$hash], $categories))
				{
					unset($handlers[$hash]);
				}
			}
		}

		return array_values($handlers);
	}

	/**
	 * @param ProcessorInterface $processor
	 *
	 * @return $this
	 */
	public function addProcessor(ProcessorInterface $processor)
	{
		$this->processors[] = $processor;

		return $this;
	}

	/**
	 * @return ProcessorInterface[]
	 */
	public function getProcessors()
	{
		return $this->processors;
	}

	/**
	 * @param bool $errorHandler     Whether to catch errors
	 * @param bool $exceptionHandler Whether to catch exceptions
	 * @param bool $shutdownHandler  Whether to register shutdown handler
	 *
	 * @return $this
	 */
	public function register($errorHandler = true, $exceptionHandler = true, $shutdownHandler = true)
	{
		if ($errorHandler)
		{
			$this->registerErrorHandler(E_ALL | E_STRICT);
		}
		if ($exceptionHandler)
		{
			$this->registerExceptionHandler();
		}
		if ($shutdownHandler)
		{
			$this->registerShutdownHandler();
		}

		return $this;
	}

	/**
	 * @param int $errorTypes
	 *
	 * @return $this
	 */
	protected function registerErrorHandler($errorTypes)
	{
		set_error_handler(array($this, 'handleError'), $errorTypes);
		$this->errorHandlerRegistered = true;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function unregisterErrorHandler()
	{
		restore_error_handler();
		$this->errorHandlerRegistered = false;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function registerExceptionHandler()
	{
		set_exception_handler(array($this, 'handleException'));
		$this->exceptionHandlerRegistered = true;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function unregisterExceptionHandler()
	{
		restore_exception_handler();
		$this->exceptionHandlerRegistered = false;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function registerShutdownHandler()
	{
		register_shutdown_function(array($this, 'handleShutdown'));
		$this->shutdownHandlerRegistered = true;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function unregisterShutdownHandler()
	{
		$this->shutdownHandlerRegistered = false;

		return $this;
	}

	/**
	 * Unregisters error & exception handler
	 *
	 * @param bool $errorHandler
	 * @param bool $exceptionHandler
	 * @param bool $shutdownHandler
	 *
	 * @return $this
	 */
	public function unregister($errorHandler = true, $exceptionHandler = true, $shutdownHandler = true)
	{
		if ($errorHandler)
		{
			$this->unregisterErrorHandler();
		}
		if ($exceptionHandler)
		{
			$this->unregisterExceptionHandler();
		}
		if ($shutdownHandler)
		{
			$this->unregisterShutdownHandler();
		}

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getErrorHandlerRegistered()
	{
		return $this->errorHandlerRegistered;
	}

	/**
	 * @return boolean
	 */
	public function getExceptionHandlerRegistered()
	{
		return $this->exceptionHandlerRegistered;
	}

	/**
	 * @return boolean
	 */
	public function getShutdownHandlerRegistered()
	{
		return $this->shutdownHandlerRegistered;
	}

	/**
	 * @param int      $errNo
	 * @param string   $errStr
	 * @param string   $errFile
	 * @param int      $errLine
	 * @param array    $errContext
	 * @param Metadata $metadata
	 *
	 * @return $this
	 */
	public function handleError($errNo, $errStr, $errFile, $errLine, $errContext = array(), Metadata $metadata = null)
	{
		$error    = ErrorException::fromPhpError($errNo, $errStr, $errFile, $errLine, $errContext);
		$metadata = $this->getMetadata($metadata, $error);

		foreach ($this->handlers as $handler)
		{
			$handler->handleError($error, $metadata);
		}

		++$this->errorsHandled;

		return $this;
	}

	/**
	 * @param \Exception $exception
	 * @param Metadata   $metadata
	 *
	 * @return $this
	 */
	public function handleException(\Exception $exception, Metadata $metadata = null)
	{
		$metadata = $this->getMetadata($metadata, $exception);

		foreach ($this->handlers as $handler)
		{
			$handler->handleException($exception, $metadata);
		}

		++$this->exceptionsHandled;

		return $this;
	}

	public function handleEvent($eventName, $message, Metadata $metadata = null)
	{
		$metadata = $this->getMetadata($metadata, null);

		foreach ($this->handlers as $handler)
		{
			$handler->handleEvent($eventName, $message, $metadata);
		}

		++$this->eventsHandled;

		return $this;
	}

	/**
	 * Handle fatal errors and such
	 *
	 * @codeCoverageIgnore
	 */
	public function handleShutdown()
	{
		if (!$this->shutdownHandlerRegistered)
		{
			return;
		}

		$error = error_get_last();
		if ($error && $this->isCatchableOnShutdown($error))
		{
			$this->handleError(
				$error['type'],
				$error['message'],
				$error['file'],
				$error['line']
			);
		}
	}

	/**
	 * @param array $error
	 *
	 * @return bool
	 */
	public static function isCatchableOnShutdown(array $error)
	{
		$severity = ErrorException::translateSeverity($error['type']);

		return $severity >= ErrorException::ERROR;
	}

	/**
	 * @return int
	 */
	public function getErrorsHandled()
	{
		return $this->errorsHandled;
	}

	/**
	 * @return int
	 */
	public function getExceptionsHandled()
	{
		return $this->exceptionsHandled;
	}

	/**
	 * @param Metadata   $metadata
	 * @param \Exception $e
	 *
	 * @return Metadata
	 */
	public function getMetadata(Metadata $metadata = null, \Exception $e = null)
	{
		$metadata = isset($metadata) ? $metadata : new Metadata();

		foreach ($this->processors as $processor)
		{
			$processor->process($metadata, $e);
		}

		return $metadata;
	}

	/**
	 * @param array $categories
	 *
	 * @return array
	 * @throws \LogicException when category is not of type "string"
	 */
	private function getCategories(array $categories = array())
	{
		array_walk($categories, function ($category)
		{
			if (!is_string($category))
			{
				throw new \LogicException('Category must be of a type "string"');
			}
		});

		return array_values($categories);
	}
}
