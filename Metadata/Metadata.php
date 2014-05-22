<?php

namespace prgTW\ErrorHandler\Metadata;

use prgTW\ErrorHandler\Error\Severity;

class Metadata
{
	/** @var array */
	protected $categories = array();

	/** @var array */
	protected $grouping = array();

	/** @var string */
	protected $appRootDir = '';

	/** @var string */
	protected $appVersion = '';

	/** @var string */
	protected $stage = '';

	/** @var array */
	protected $tags = array();

	/** @var array */
	protected $metadata = array();

	/** @var int */
	protected $severity;

	/**
	 * @param string $stage
	 *
	 * @return $this
	 */
	public function setStage($stage)
	{
		$this->stage = $stage;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStage()
	{
		return $this->stage;
	}

	/**
	 * @param string $appVersion
	 *
	 * @return $this
	 */
	public function setAppVersion($appVersion)
	{
		$this->appVersion = $appVersion;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAppVersion()
	{
		return $this->appVersion;
	}

	/**
	 * @param string $appRootDir
	 *
	 * @return $this
	 */
	public function setAppRootDir($appRootDir)
	{
		$this->appRootDir = $appRootDir;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getAppRootDir()
	{
		return $this->appRootDir;
	}

	/**
	 * @return array
	 */
	public function getCategories()
	{
		return array_values($this->categories);
	}

	/**
	 * @param array $categories
	 *
	 * @throws \LogicException when category is not of type "string"
	 * @return $this
	 */
	public function addCategories(array $categories)
	{
		$this->categories = array();
		foreach ($categories as $category)
		{
			$this->addCategory($category);
		}

		return $this;
	}

	/**
	 * @param string $category
	 *
	 * @return $this
	 * @throws \LogicException when category is not of type "string"
	 */
	public function addCategory($category)
	{
		if (!is_string($category))
		{
			// @codeCoverageIgnoreStart
			throw new \LogicException('Category must be of a type "string"');
			// @codeCoverageIgnoreEnd
		}
		$this->categories[$category] = $category;

		return $this;
	}

	/**
	 * @param $category
	 *
	 * @return $this
	 * @throws \LogicException when category is not of type "string"
	 */
	public function removeCategory($category)
	{
		if (!is_string($category))
		{
			// @codeCoverageIgnoreStart
			throw new \LogicException('Category must be of a type "string"');
			// @codeCoverageIgnoreEnd
		}
		if (isset($this->categories[$category]))
		{
			unset($this->categories[$category]);
		}

		return $this;
	}

	/**
	 * @param array $grouping
	 *
	 * @return $this
	 */
	public function addGrouping(array $grouping)
	{
		foreach ($grouping as $name => $value)
		{
			$this->groupBy($name, $value);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function getGrouping()
	{
		$grouping = $this->grouping;
		ksort($grouping);

		return $grouping;
	}

	/**
	 * @param string $name
	 * @param string $value
	 *
	 * @return $this
	 */
	public function groupBy($name, $value)
	{
		$this->grouping[$name] = $value;

		return $this;
	}

	/**
	 * @param array $tags
	 *
	 * @return $this
	 */
	public function addTags(array $tags)
	{
		$this->tags = array_merge_recursive($this->tags, $tags);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function addTag($name, $value)
	{
		$this->tags[$name] = $value;

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function removeTag($name)
	{
		if (isset($this->tags[$name]))
		{
			unset($this->tags[$name]);
		}

		return $this;
	}

	/**
	 * @param array $metadata
	 *
	 * @return $this
	 */
	public function addMetadata(array $metadata)
	{
		$this->metadata = array_merge_recursive($this->metadata, $metadata);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function addMetadatum($name, $value)
	{
		$this->metadata[$name] = $value;

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function removeMetadatum($name)
	{
		if (isset($this->metadata[$name]))
		{
			unset($this->metadata[$name]);
		}

		return $this;
	}

	/**
	 * @param int $severity
	 *
	 * @see Severity::$SEVERITIES
	 * @throws \LogicException when severity is not in a given array
	 * @return $this
	 */
	public function setSeverity($severity)
	{
		if (!in_array($severity, Severity::$SEVERITIES))
		{
			// @codeCoverageIgnoreStart
			$severities = array_map(function ($severity)
			{
				return sprintf('Severity::%s', $severity);
			}, array_keys(Severity::$SEVERITIES));
			throw new \LogicException(sprintf('Severity must be one of: %s', $severities));
			// @codeCoverageIgnoreEnd
		}
		$this->severity = $severity;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getSeverity()
	{
		return $this->severity;
	}
}
