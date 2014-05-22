<?php

namespace prgTW\ErrorHandler\Metadata;

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
	 * @param array $categories
	 *
	 * @throws \LogicException when category is not of type "string"
	 * @return $this
	 */
	public function setCategories(array $categories)
	{
		$this->categories = array();
		foreach ($categories as $category)
		{
			$this->addCategory($category);
		}

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getCategories()
	{
		return array_values($this->categories);
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
	public function setGrouping(array $grouping)
	{
		$this->grouping = $grouping;

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
	public function setTags(array $tags)
	{
		$this->tags = $tags;

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
	public function setTag($name, $value)
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
	public function setMetadata(array $metadata)
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
	public function setMetadatum($name, $value)
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
}
