<?php

namespace prgTW\ErrorHandler\Error;

class Severity
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
	public static $SEVERITIES = array(
		'DEBUG'     => self::DEBUG,
		'INFO'      => self::INFO,
		'NOTICE'    => self::NOTICE,
		'WARNING'   => self::WARNING,
		'ERROR'     => self::ERROR,
		'CRITICAL'  => self::CRITICAL,
		'ALERT'     => self::ALERT,
		'EMERGENCY' => self::EMERGENCY
	);

	/**
	 * @param int $errorNo
	 *
	 * @return int
	 */
	public static function fromPhpErrorNo($errorNo)
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
			case E_DEPRECATED:
				return self::WARNING;
			case E_USER_DEPRECATED:
				return self::WARNING;
		}

		return self::ERROR;
	}
}
