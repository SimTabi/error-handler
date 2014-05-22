<?php

namespace prgTW\ErrorHandler\Utils;

use prgTW\ErrorHandler\Error\Severity;

class Utils
{
	/**
	 * @param array $error
	 *
	 * @return bool
	 */
	public static function isCatchableOnShutdown(array $error)
	{
		$severity = Severity::fromPhpErrorNo($error['type']);

		return $severity >= Severity::ERROR;
	}
}
