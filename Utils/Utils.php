<?php

namespace prgTW\ErrorHandler\Utils;

use prgTW\ErrorHandler\Error\ErrorException;

class Utils
{
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
}
