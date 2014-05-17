<?php

namespace prgTW\ErrorHandler\Metadata;

class Metadata
{
	/** @var string */
	protected $appName = '';

	/** @var string */
	protected $appRootDir = '';

	/** @var string */
	protected $stage = '';

	/** @var string */
	protected $revision = '';

	/** @var array */
	protected $tags = array();

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
	 * @param string $revision
	 *
	 * @return $this
	 */
	public function setRevision($revision)
	{
		$this->revision = $revision;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRevision()
	{
		return $this->revision;
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
	 * @param string $appName
	 *
	 * @return $this
	 */
	public function setAppName($appName)
	{
		$this->appName = $appName;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getAppName()
	{
		return $this->appName;
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
	 * @param string $tagName
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function setTag($tagName, $value)
	{
		$this->tags[$tagName] = $value;

		return $this;
	}

	/**
	 * @param string $tagName
	 *
	 * @return $this
	 */
	public function removeTag($tagName)
	{
		if (isset($this->tags[$tagName]))
		{
			unset($this->tags[$tagName]);
		}

		return $this;
	}

}
