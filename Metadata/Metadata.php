<?php

namespace prgTW\ErrorHandler\Metadata;

class Metadata
{
	/** @var string */
	protected $category = '';

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
	 * @param string $category
	 *
	 * @return $this
	 */
	public function setCategory($category)
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getCategory()
	{
		return $this->category;
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
		$this->metadata = $metadata;

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
