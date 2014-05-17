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

	/** @var HandlerInterface[] */
	protected $handlers = array();

	/** @var ProcessorInterface[] */
	protected $processors = array();

	/**
	 * @param HandlerInterface $handler
	 *
	 * @return $this
	 */
	public function addHandler(HandlerInterface $handler)
	{
		$this->handlers[] = $handler;

		return $this;
	}

	/**
	 * @return HandlerInterface[]
	 */
	public function getHandlers()
	{
		return $this->handlers;
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

	/** {@inheritdoc} */
	public function handleError($errNo, $errStr, $errFile, $errLine, $errContext = array())
	{
		$error    = ErrorException::fromPhpError($errNo, $errStr, $errFile, $errLine, $errContext);
		$metadata = $this->getMetadata($error);

		foreach ($this->handlers as $handler)
		{
			$handler->handleError($error, $metadata);
		}

		++$this->errorsHandled;

		return $this;
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception)
	{
		$metadata = $this->getMetadata($exception);

		foreach ($this->handlers as $handler)
		{
			$handler->handleException($exception, $metadata);
		}

		++$this->exceptionsHandled;

		return $this;
	}

	/**
	 * Handle fatal errors and such
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
	 * @param \Exception $e
	 *
	 * @return Metadata
	 */
	public function getMetadata(\Exception $e)
	{
		$metadata = new Metadata();

		foreach ($this->processors as $processor)
		{
			$processor->process($metadata, $e);
		}

		return $metadata;
	}
}
