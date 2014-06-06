<?php

namespace prgTW\ErrorHandler;

use prgTW\ErrorHandler\Collector\StatsCollector;
use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Error\Severity;
use prgTW\ErrorHandler\Handler\HandlerManager;
use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorManager;

class ErrorHandler
{
	/** @var bool */
	protected $errorHandlerRegistered = false;

	/** @var bool */
	protected $exceptionHandlerRegistered = false;

	/** @var bool */
	protected $shutdownHandlerRegistered = false;

	/** @var HandlerManager */
	protected $handlerManager;

	/** @var ProcessorManager */
	protected $processorManager;

	/** @var StatsCollector */
	protected $stats;

	/** @var int */
	protected $minSeverityOnShutdown;

	/**
	 * @param int $minSeverityOnShutdown Minimum severity of on-shutdown-captured errors to be handled
	 */
	public function __construct($minSeverityOnShutdown = Severity::ERROR)
	{
		$this->minSeverityOnShutdown = $minSeverityOnShutdown;
		$this->handlerManager        = new HandlerManager();
		$this->processorManager      = new ProcessorManager();
		$this->stats                 = new StatsCollector();
	}

	/**
	 * @return HandlerManager
	 */
	public function getHandlerManager()
	{
		return $this->handlerManager;
	}

	/**
	 * @return ProcessorManager
	 */
	public function getProcessorManager()
	{
		return $this->processorManager;
	}

	/**
	 * @return StatsCollector
	 */
	public function getStats()
	{
		return $this->stats;
	}

	/**
	 * @return int
	 */
	public function getMinSeverityOnShutdown()
	{
		return $this->minSeverityOnShutdown;
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
		$error      = ErrorException::fromPhpError($errNo, $errStr, $errFile, $errLine, $errContext);
		$metadata   = $this->getMetadata($metadata, $error);
		$categories = $metadata->getCategories();

		foreach ($this->handlerManager->all($categories) as $handler)
		{
			$handler->handleError($error, $metadata);
		}

		if ($this->stats)
		{
			$this->stats->addError();
		}

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
		$metadata   = $this->getMetadata($metadata, $exception);
		$categories = $metadata->getCategories();

		foreach ($this->handlerManager->all($categories) as $handler)
		{
			$handler->handleException($exception, $metadata);
		}

		if ($this->stats)
		{
			$this->stats->addException();
		}

		return $this;
	}

	/**
	 * @param string   $event
	 * @param Metadata $metadata
	 *
	 * @return $this
	 */
	public function handleEvent($event, Metadata $metadata = null)
	{
		$metadata   = $this->getMetadata($metadata, null);
		$categories = $metadata->getCategories();

		foreach ($this->handlerManager->all($categories) as $handler)
		{
			$handler->handleEvent($event, $metadata);
		}

		if ($this->stats)
		{
			$this->stats->addEvent();
		}

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
		if ($error && Severity::fromPhpErrorNo($error['type']) >= $this->minSeverityOnShutdown)
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
	 * @param Metadata   $metadata
	 * @param \Exception $exception
	 *
	 * @return Metadata
	 */
	public function getMetadata(Metadata $metadata = null, \Exception $exception = null)
	{
		$metadata = isset($metadata) ? $metadata : new Metadata();

		if (!$metadata->getSeverity())
		{
			if ($exception instanceof ErrorException)
			{
				$metadata->setSeverity($exception->getSeverity());
			}
			elseif ($exception instanceof \Exception)
			{
				$metadata->setSeverity(Severity::ERROR);
			}
			else
			{
				$metadata->setSeverity(Severity::NOTICE);
			}
		}

		foreach ($this->processorManager->all() as $processor)
		{
			$processor->process($metadata, $exception);
		}

		return $metadata;
	}
}
