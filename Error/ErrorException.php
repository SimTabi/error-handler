<?php

namespace prgTW\ErrorHandler\Error;

class ErrorException extends \ErrorException
{
	const DEBUG     = 100;
	const INFO      = 200;
	const NOTICE    = 250;
	const WARNING   = 300;
	const ERROR     = 400;
	const CRITICAL  = 500;
	const ALERT     = 550;
	const EMERGENCY = 600;

	/** @var array */
	public static $phpErrors = array(
		0                   => 'UNKNOWN',
		E_NOTICE            => 'E_NOTICE',
		E_WARNING           => 'E_WARNING',
		E_ERROR             => 'E_ERROR',
		E_PARSE             => 'E_PARSE',
		E_CORE_ERROR        => 'E_CORE_ERROR',
		E_CORE_WARNING      => 'E_CORE_WARNING',
		E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
		E_USER_ERROR        => 'E_USER_ERROR',
		E_USER_WARNING      => 'E_USER_WARNING',
		E_USER_NOTICE       => 'E_USER_NOTICE',
		E_STRICT            => 'E_STRICT',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED        => 'E_DEPRECATED',
		E_USER_DEPRECATED   => 'E_USER_DEPRECATED'
	);

	/** @var array */
	protected $context;

	public function __construct($message = '', $code = 0, $filename = __FILE__, $lineNo = __LINE__, array $context = array())
	{
		$severity = self::translateSeverity($code);
		parent::__construct($message, $code, $severity, $filename, $lineNo);
		$this->context = $context;
	}

	/**
	 * @return array
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * @param int    $errNo
	 * @param string $errStr
	 * @param string $errFile
	 * @param int    $errLine
	 * @param array  $errContext
	 *
	 * @return ErrorException
	 */
	public static function fromPhpError($errNo, $errStr, $errFile, $errLine, $errContext = array())
	{
		$message = sprintf('[%s] %s in %s:%d', self::$phpErrors[$errNo], $errStr, $errFile, $errLine);

		$exception = new ErrorException($message, $errNo, $errFile, $errLine, $errContext);

		return $exception;
	}

	/**
	 * @param $errorNo
	 * @codeCoverageIgnore
	 *
	 * @return int
	 */
	public static function translateSeverity($errorNo)
	{
		switch ($errorNo)
		{
			case E_ERROR:
				return self::ERROR;
			case E_WARNING:
				return self::WARNING;
			case E_PARSE:
				return self::ERROR;
			case E_NOTICE:
				return self::NOTICE;
			case E_CORE_ERROR:
				return self::ERROR;
			case E_CORE_WARNING:
				return self::WARNING;
			case E_COMPILE_ERROR:
				return self::ERROR;
			case E_COMPILE_WARNING:
				return self::WARNING;
			case E_USER_ERROR:
				return self::ERROR;
			case E_USER_WARNING:
				return self::WARNING;
			case E_USER_NOTICE:
				return self::NOTICE;
			case E_STRICT:
				return self::INFO;
			case E_RECOVERABLE_ERROR:
				return self::ERROR;
		}
		if (version_compare(PHP_VERSION, '5.3.0', '>='))
		{
			switch ($errorNo)
			{
				case E_DEPRECATED:
					return self::WARNING;
				case E_USER_DEPRECATED:
					return self::WARNING;
			}
		}

		return self::ERROR;
	}
}
