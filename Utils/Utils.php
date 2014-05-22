<?php

namespace prgTW\ErrorHandler\Utils;

class Utils
{
	/**
	 * @param array $categories
	 *
	 * @return array
	 * @throws \LogicException when category is not of type "string"
	 */
	public static function getCategories(array $categories = array())
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
